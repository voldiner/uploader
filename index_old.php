<?php
error_reporting(E_ALL);
//--------- для отладки ------------------------
require '../vendor/autoload.php';
require 'handler.php';
//----------------------------------------------
// ---------- параметри константи --------------
//ini_set('display_errors', 'on');
define("IS_ECHO", true);          // вивід повідомлень в браузер
define("TIME_TO_RUN", 1);        // час в сек. якщо скрипт не оновлював мітку, то вважаемо що він не працює
//define("TIME_PAUSE", 2);        // час в сек. через який буде здійснюватися сканування катаогів
//define("NUMBER_CYCLE_TO_SEND", 5);        // кількість пустих циклів до відправки 1 -> негайна відправка
//
//$folders = [                      // папки за якими ведеться моніторинг
//    [
//        'name_folder' => 'f:\vopas\test1\\',
//        'ftp_login' => 'voldiner0953',
//        'ftp_password' => 'INsdbTYeWB',
//        'ftp_hostname' => 's1.ho.ua',
//        'uri' => '',
//        'count_to_send' => 0,        // must be 0 !!!
//        'files' => [],              // must be [] !!!
//    ],
//    [
//        'name_folder' => 'f:\vopas\test2\\',
//        'ftp_login' => 'voldiner0953',
//        'ftp_password' => 'INsdbTYeWB',
//        'ftp_hostname' => 's1.ho.ua',
//        'uri' => '',
//        'count_to_send' => 0,
//        'files' => [],
//    ],
//];
//----------------------------------------------
// ------- check if process steel running ------ //
//if (file_exists('pid.txt')) {
//    $content_pid = file_get_contents('pid.txt');
//    //dd($content_pid);
//    if ($content_pid && is_numeric($content_pid)) {
//        //dump(time()-$content_pid);
//        if (time() - $content_pid < TIME_TO_RUN) {
//            if (IS_ECHO) echo 'end ...process steel running <br />';
//            //dd('error time');
//            exit();
//        }
//    } else {
//        if (IS_ECHO) echo 'end ...process steel running <br />';
//        //dd('error time');
//        exit();
//    }
//}

//------- починаємо роботу ------------------------
//require 'functions.php';
//include 'errors.php';  // for testing error handler
//ignore_user_abort(true);
//
//register_shutdown_function('shutdown');
//set_error_handler('myHandler');
//
//file_put_contents('pid.txt', time());
//
//$data = 'process starting ...' . date('d m Y H:i:s') . PHP_EOL;
//saveToLog($data);
exit('ok2');

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$vivod = 1;
$col = 1;

//if (IS_ECHO) echo 'Begin ...<br />';

// ----------- основний цикл скрипта ----------------- //

while (true) {
    // ------ stop process -------- //
//    if (!file_exists('pid.txt')) {
//        break;
//    }
//    $info = null;
//    foreach ($folders as &$folder) {
//        //dump($folder['count_to_send'], $folder['name_folder']);
//        if ($folder['count_to_send'] === 0) {  // читаємо папку і ставимо на чергу
//
//            $folder['files'] = array_diff(scandir($folder['name_folder']), array('..', '.'));
//            //dump($folder['files']);
//            if (is_null($folder['files'])) {
//                if (IS_ECHO) echo "Помилка читання {$folder['name_folder']} <br>";
//                saveToLog("Помилка читання {$folder['name_folder']}");
//                $folder['files'] = [];
//                continue;
//            }
//            if (count($folder['files']) === 0) {    // відсутні файли
//                continue;
//            }
//
//        }
//        // просто чекаємо поки файли можна буде відправвити
//
//
//        if (NUMBER_CYCLE_TO_SEND === ++$folder['count_to_send']) {
//            // ----- відправляємо файли по ftp на сайт ------- //
//            $ftp_errors = transfer_ftp($folder);
//            dump($ftp_errors);
//            if ($ftp_errors) {
//                foreach ($ftp_errors as $ftp_error) {
//                    if (IS_ECHO) echo $ftp_error . '<br>';
//                    saveToLog($ftp_error);
//                }
//            }

            // ----- запускаємо скрипт на сайті ------------- //
            /* curl_setopt($curl, CURLOPT_URL, $folder['uri']);
             curl_setopt($curl, CURLOPT_POSTFIELDS, $toSend);
             $result = curl_exec($curl);
             if ($result) {
                 $data = 'data send ok ' . date('d m Y H:i:s') . PHP_EOL;
                 file_put_contents($filelog, $data, FILE_APPEND);
             } else {
                 $data = 'data send error ' . date('d m Y H:i:s') . PHP_EOL;
                 file_put_contents($filelog, $data, FILE_APPEND);
             }*/

            // ----- після відпрвки не забути видалити відправлені файли -----

//            foreach ($folder['files'] as $file){
//                unlink($folder['name_folder'].$file);
//            }
            // ----- почистити масив файлів -----------------




            // ----- обнуляємо лічильник
           // $folder['count_to_send'] = 0;
//        }
//    }
   //dd('stop');
//
//    $vivod++;
//    if ($col > 40) {
//        if (IS_ECHO) echo $vivod . '<br>';
//        $col = 0;
//    } else {
//        if (IS_ECHO) echo $vivod;
//    }
//    $col++;
//    if (IS_ECHO) flush();
//    //ob_flush();
//    sleep(TIME_PAUSE);
}
$my_error = false;
echo 'stop';


