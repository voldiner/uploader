<?php
error_reporting(E_ALL);
//--------- для отладки ------------------------
require '../vendor/autoload.php';
require 'handler.php';
// ---------- параметри константи --------------
ini_set('display_errors', 'on');
define("IS_ECHO", true);          // вивід повідомлень в браузер
define("TIME_TO_RUN", 10);        // час в сек. якщо скрипт не оновлював мітку, то вважаемо що він не працює
define("TIME_PAUSE", 2);        // час в сек. через який буде здійснюватися сканування катаогів
define("NUMBER_CYCLE_TO_SEND", 2);        // 15 кількість пустих циклів до відправки 1 -> негайна відправка
define("STATION", 3);

$folders = require 'config\config.php';

//----------------------------------------------
// ------- check if process steel running ------ //
if (file_exists('pid.txt')) {
    $content_pid = file_get_contents('pid.txt');

    if ($content_pid && is_numeric($content_pid)) {

        if (time() - $content_pid < TIME_TO_RUN) {
            if (IS_ECHO) echo 'end ...process steel running <br />';
            exit();
        }
    } else {
        //if (IS_ECHO) echo 'end ...process steel running <br />';
        //dd('error time');
        //exit();
    }
}

//------- починаємо роботу ------------------------
require 'functions.php';
//include 'errors.php';  // for testing error handler
ignore_user_abort(true);

register_shutdown_function('shutdown');
set_error_handler('myHandler');

file_put_contents('pid.txt', time());
file_put_contents('stop.txt', 'delete for stop script !!!');
$data = 'process starting ...' . date('d m Y H:i:s') . PHP_EOL;
saveToLog($data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
$vivod = 1;
$col = 1;

if (IS_ECHO) echo 'Begin ...<br />';

// ----------- основний цикл скрипта ----------------- //

while (true) {
    // ------ stop process -------- //
    if (!file_exists('stop.txt')) {
        break;
    }
    $info = null;
    foreach ($folders as &$folder) {
        if ($folder['count_to_send'] === 0) {  // читаємо папку і ставимо на чергу
            $folder['files'] = scandir($folder['name_folder']);
            //dd($folder['files']);
            if ($folder['files']===false) {
                if (IS_ECHO) echo "Помилка читання {$folder['name_folder']} <br>";
                saveToLog("Помилка читання {$folder['name_folder']}");
                $folder['files'] = [];
                continue;
            }
            $folder['files'] = array_diff($folder['files'], array('..', '.'));
            //array_walk($folder['files'], 'array_to_lover');
            if (count($folder['files']) === 0) {    // відсутні файли
                continue;
            } else {
                if (IS_ECHO) echo 'Поставив на чергу <br>';
            }

        }
        //-- просто чекаємо поки файли можна буде відправвити

        if (NUMBER_CYCLE_TO_SEND === ++$folder['count_to_send']) {
            // ----- відправляємо файли по ftp на сайт ------- //
            $ftp_errors = transfer_ftp($folder);
            if ($ftp_errors) {
                foreach ($ftp_errors as $ftp_error) {
                    if (IS_ECHO) echo $ftp_error . '<br>';
                    saveToLog($ftp_error);
                }

                // ----- якщо спроба невдала, перемістим файл в окрему папку
                // ----- до кожного файлу додамо префікс, щоб не накладалися
                // ----- і скрипт не будемо запускати
                copyToFailed($folder);
                // ----- видалим файли
                foreach ($folder['files'] as $file) {
                    unlink($folder['name_folder'] . $file);
                }
                // ----- повідомимо на megalog ------- //
                // ---- запишемо тільки першу помилку в megalog ------ //
                $toSend = [
                    'result' => 0,
                    'files' => $folder['files'],
                    'error' => $ftp_errors[0],
                    'category_id' => 2,
                    'alias' =>  $folder['alias'],
                    'station_id' => STATION,
                    'login' => 'vold',
                    'password' => 'vold',
                ];
                megalog($toSend);
                // ----- обнуляємо лічильник --------- //
                $folder['count_to_send'] = 0;
                continue;
            }
            if (isset($folder['include'])){
                if (file_exists($folder['include'])){
                    include $folder['include'];
                }else{
                    $result = false;
                }
            }else{
                // ----- запускаємо скрипт на сайті ------------- //
                curl_setopt($curl, CURLOPT_URL, $folder['uri']);
                $result = curl_exec($curl);
            }

             // ---- можливі помилки ---------------
             if ($result === false) {
                 $textError = 'Невідома помилка запуску скрипта ' . $folder['uri'] . ' ' . curl_error($curl);
                 saveToLog($textError . ' ' . date('d m Y H:i:s'));
                 if (IS_ECHO) echo $textError . '<br>';
                 copyToFailed($folder);
                 $toSend = [
                     'result' => 0,
                     'files' => $folder['files'],
                     'error' => $textError,
                     'categoty_id' => 3,
                     'alias' => $folder['alias'],
                     'station_id' => STATION,
                     'login' => 'vold',
                     'password' => 'vold',
                 ];
                 megalog($toSend);
             }elseif(isset($text_msg)){
                 $textError = 'Помилка запуску скрипта ' . $folder['uri'] . ' ' . $text_msg;
                 saveToLog($textError . ' ' . date('d m Y H:i:s'));
                 if (IS_ECHO) echo $textError . '<br>';
                 copyToFailed($folder);
                 $toSend = [
                     'result' => 0,
                     'files' => $folder['files'],
                     'error' => $textError,
                     'categoty_id' => 3,
                     'alias' => $folder['alias'],
                     'station_id' => STATION,
                     'login' => 'vold',
                     'password' => 'vold',
                 ];
                 megalog($toSend);
             } elseif (in_array($result, $folder['errors'])) {
                 $textError = "Помилка запуску скрипта {$folder['uri']} -> $result ";
                 saveToLog( $textError . date('d m Y H:i:s'));
                 if (IS_ECHO) echo $textError . '<br>';
                 copyToFailed($folder);
                 $toSend = [
                     'result' => 0,
                     'files' =>  $folder['files'],
                     'error' => $textError,
                     'categoty_id' => 3,
                     'alias' =>  $folder['alias'],
                     'station_id' => STATION,
                     'login' => 'vold',
                     'password' => 'vold',
                 ];

                 megalog($toSend);
             }else{
                 saveToLog("Успішний запуск скрипта {$folder['uri']} " . date('d m Y H:i:s'));
                 copyToArchive($folder);
                 if (IS_ECHO) echo "Успішний запуск скрипта {$folder['uri']} ";

                 // ------ передаємо параметри скрипту для запису в БД на сайті статистики
                 $toSend = [
                     'result' => 1,
                     'files' => $folder['files'],
                     //'error' => null,
                     //'category_id' => null,
                     'alias' =>  $folder['alias'],
                     'station_id' => STATION,
                     'login' => 'vold',
                     'password' => 'vold',
                 ];
                 megalog($toSend);

             }
            // ----- після відпрвки не забути видалити відправлені файли -----

            foreach ($folder['files'] as $file) {
                unlink($folder['name_folder'] . $file);
            }

            // ----- обнуляємо лічильник
            $folder['count_to_send'] = 0;
        }
    }

    $vivod++;
    if ($col > 40) {
        if (IS_ECHO) echo $vivod . '<br>';
        $col = 0;
    } else {
        if (IS_ECHO) echo $vivod;
    }
    $col++;
    if (IS_ECHO) flush();
    //ob_flush();
    sleep(TIME_PAUSE);
    file_put_contents('pid.txt', time());
}
$my_error = false;
if (IS_ECHO) echo 'stop';


