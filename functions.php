<?php
//function check_upload_data($kol_day,$from_ac,$to_ac='Луцьк'){
//    global $pattern;
//    $result = '';
//    for ($i=1; $i<=$kol_day;$i++){
//        $date_for_get =  date('d.m.Y',strtotime("+$i day"));
//        $subject = file_get_contents("https://www.vopas.com.com.ua/search/?from=$from_ac&to=$to_ac&date=$date_for_get&time=00+%3A+00");
//        if (preg_match($pattern,$subject)){
//            $result .= $date_for_get.' - data is out!! <br>';
//        }else{
//            $result .= $date_for_get.' - Data upload success!! <br>';
//        }
//    }
//    return $result;
//}
function megalog($toSend)
{
    //$url = 'http://megalog/api/posts/add';
    $url = 'http://voldiner0953.ho.ua/api/posts/add';

    $toSend = json_encode($toSend);
    $curlMega = curl_init();
    curl_setopt($curlMega, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curlMega, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlMega, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlMega, CURLOPT_URL, $url);
    curl_setopt($curlMega, CURLOPT_POSTFIELDS, $toSend);
    $result = curl_exec($curlMega);
    curl_close($curlMega);
    return $result;
}


function saveToLog($content){
    $filelog = __DIR__.'\log\\'.date('d_m_Y').'.log';
    $content .= PHP_EOL;
    file_put_contents($filelog,$content,FILE_APPEND);
}

function copyToFailed($folder){
    if ($folder['copyToFail']){
        foreach ($folder['files'] as $file) {
            copy($folder['name_folder'] . $file, 'failed\\' .date('d_m_Y'). '_'. mt_rand(1,1000). $file);
        }
    }
}
function copyToArchive($folder){
    $dateTime = $folder['copy_folder_data_time'] ? date('d_m_Y_H_i_s') : '';
    if ($folder['copy_folder']){
        foreach ($folder['files'] as $file) {
            copy($folder['name_folder'] . $file, $folder['copy_folder'] . $dateTime . $file);
        }
    }
}

function transfer_ftp($folder){
    //dump($folder);
    $ftp_errors = [];
    if ($folder['ftp_hostname'] === false){
        return $ftp_errors;
    }
    $connect = ftp_connect($folder['ftp_hostname']);

    if ($connect !== false) {
        if (@ftp_login($connect, $folder['ftp_login'], $folder['ftp_password'])) {

            foreach ($folder['files'] as $file) {
                if (ftp_put($connect, $folder['ftp_folder'] . $file, $folder['name_folder'] . $file, FTP_BINARY)) {
                    // включение пассивного режима використовуємо для ас Нововолинськ
                    //ftp_pasv($connect, true);
                    if (IS_ECHO) echo $folder['ftp_folder'] . $file . ' успешно загружен на сервер <br>';
                    saveToLog($folder['ftp_folder'] . $file . ' успешно загружен на сервер');

                } else {
                    $ftp_errors[] = 'ошибка загрузки на сервер файл ' . $folder['ftp_folder'] . $file;
                }
            }
        } else {
            $ftp_errors[]= "login error {$folder['ftp_login']} -> {$folder['ftp_password']}";
        }
        ftp_close($connect);
    } else {
        $ftp_errors[] = "Error ftp_connect {$folder['ftp_hostname']}";
    };

    return $ftp_errors;
}


function shutdown()
{
    $fileError = __DIR__.'\pid.txt';
    //dd($filename);
    $error = error_get_last();
    //echo $error['type'];
    if (isset($error['type']) & in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR])){

        $data = ' ** process shutdown error...'.PHP_EOL;
        // выводим текст ошибки  в лог
        $data .= " ** ".getErrorForCode($error['type']).": {$error['message']} ".PHP_EOL;
        $data .= " ** File: {$error['file']}:{$error['line']}".PHP_EOL;
        $data .= ' ** '. date('d m Y H:i:s') . PHP_EOL;
        unlink($fileError);
    }else{
        $data = 'process shutdown manually...' . date('d m Y H:i:s') . PHP_EOL;
    }
    saveToLog($data);

    //добавити параметер щоб знати чи це аваріне завершення чи по exit()
    // Это наша завершающая функция,
    // здесь мы можем выполнить все последние операции
    // перед тем как скрипт полностью завершится.
    // використати константу __DIR__ бо може змінитися поточна директорія
    // виводити echo не можна - не буде працювати в нових версіях вроді виправлено
    //unlink('pid.txt');
    //echo 'Скрипт успешно завершился', PHP_EOL;
}
function getErrorForCode($code){
    $errors = [
        E_ERROR             => 'ERROR',
        E_WARNING           => 'WARNING',
        E_PARSE             => 'PARSE',
        E_NOTICE            => 'NOTICE',
        E_CORE_ERROR        => 'CORE_ERROR',
        E_CORE_WARNING      => 'CORE_WARNING',
        E_COMPILE_ERROR     => 'COMPILE_ERROR',
        E_COMPILE_WARNING   => 'COMPILE_WARNING',
        E_USER_ERROR        => 'USER_ERROR',
        E_USER_WARNING      => 'USER_WARNING',
        E_USER_NOTICE       => 'USER_NOTICE',
        E_STRICT            => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED        => 'DEPRECATED',
        E_USER_DEPRECATED   => 'USER_DEPRECATED',
    ];
    if(array_key_exists($code, $errors)){
        return $errors[$code];
    }
    return 'not found error';
}
function parser(){
    // вертає array якщо файл пропарсений і записаний в масив
    // або false якщо парсинг не вдався
    // прочитати файл (file())- перевірити останній символ @ і відрізати його
    // розпарсити кожен елемент масиву
    //
    $content = file('from_pc.txt',FILE_IGNORE_NEW_LINES);

    if (!is_array($content) && $content === false){
        return false;
    }
    if ($content[count($content)-1] !== '@'){
        return false;
    }
    $result = [];
    foreach ($content as $item){
        if ($item !== '@'){
            $result[] = mb_convert_encoding($item, "UTF-8","CP866");
        }
    }
    unlink('from_pc.txt');
    return $result;
}
function array_to_lover(&$item1, $key)
{
    $item1 = strtolower($item1);
}
//function ping($host, $port, $timeout) {
//Echoing it will display the ping if the host is up, if not it'll say "down".
//    $tB = microtime(true);
//    $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);
//    if (!$fP) { return "down"; }
//    $tA = microtime(true);
//    return round((($tA - $tB) * 1000), 0)." ms";
//}
// наш обработчик ошибок
function myHandler($level, $message, $file, $line, $context) {
    //global $filelog;
    //echo '1';
    // в зависимости от типа ошибки формируем заголовок сообщения
//    switch ($level) {
//        case E_WARNING:
//            $type = 'Warning';
//            break;
//        case E_NOTICE:
//            $type = 'Notice';
//            break;
//            default;
//            // это не E_WARNING и не E_NOTICE
//            // значит мы прекращаем обработку ошибки
//            // далее обработка ложится на сам PHP
//            return false;
//    }
    // выводим текст ошибки  в лог
    $data = " ** ".getErrorForCode($level).": $message ".PHP_EOL;
    //$data .= " ** File: $file:$line".PHP_EOL;
    $data .= ' ** '. date('d m Y H:i:s') . PHP_EOL;
    saveToLog($data);
    // сообщаем, что мы обработали ошибку, и дальнейшая обработка не требуется - return true
    return true; // передаем на обработку php
}
/*
    * функции перевода смс в транслит
    */

// $str - текст сообщения в кириллице

function sms_translit($str)
{
    $translit = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
    );
    return strtr($str,$translit);
}