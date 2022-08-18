<?php
$toPostParam = [
 'data' => json_encode(['files' =>$folder['files'], 'folder' => $folder['name_folder']])
];
$curlReport = curl_init();
curl_setopt($curlReport, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlReport, CURLOPT_URL, $folder['uri']);

$ret_curl = curl_exec($curlMail);

curl_close($curlReport);

if ($ret_curl === false){
    $result = $ret_curl;
}else{
    $report_response = json_decode($ret_curl,true);
    if (is_array($report_response)){
        if (isset($report_errors['error'])) {
            $text_msg = $report_errors['error'];
        }else{
            unset($text_msg);
        }
    }else{
        $result = false;
    }
}


