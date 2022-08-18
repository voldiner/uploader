<?php
$toPostParam = [
 'data' => json_encode(['files' =>$folder['files'], 'folder' => $folder['name_folder']])
];
$curlMail = curl_init();
curl_setopt($curlMail, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlMail, CURLOPT_URL, $folder['uri']);
curl_setopt($curlMail, CURLOPT_POSTFIELDS, $toPostParam);

$ret_curl = curl_exec($curlMail);

curl_close($curlMail);

if ($ret_curl === false){
    $result = $ret_curl;
}else{
    $email_errors = json_decode($ret_curl,true);
    if (is_array($email_errors)){
        if (count($email_errors)) {
            $text_msg = '';
            foreach ($email_errors as $email_error) {
                $text_msg .= $email_error['message'] . ' -> ' . $email_error['file'];
            }
        }else{
            unset($text_msg);
        }
    }else{
        $result = false;
    }
}


