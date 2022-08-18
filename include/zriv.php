<?php
/**
 *  формує url для запиту скрипта на закриття рейсу
 *  посилає запит на відповідний адрес та обробляє результат
 */

foreach ($folder['files'] as $file) {
    if (!file_exists($folder['name_folder'] . $file)){
        $text_msg .= 'no exists file ' . $folder['name_folder'] . $file;
        continue;
    }
    $getParam = file_get_contents($folder['name_folder'] . $file);
    if (!$getParam) {
        $text_msg .= 'error reading file ' . $folder['name_folder'] . $file;
        continue;
    }
	$pos = strpos($getParam, '?');
	$getParam = substr($getParam, $pos);
    $url = $folder['uri'] . $getParam;

    $result_zriv = send_request_zriv($url);

    if ($result_zriv !== true) {
        $text_msg .=  $result_zriv . $folder['name_folder'] . $file;
        continue;
    }

}
