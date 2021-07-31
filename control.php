<?php
/**
 * відпрака запиту на megalog який буде здійснювати контроль
 * завантаження і в разі відсутності повідомлень
 * в свою чергу буде відсилати повідомлення на e-mail
 */

//$url = 'http://megalog/api/posts/add';
$url = 'http://voldiner0953.ho.ua/control';

$curlMega = curl_init();
//curl_setopt($curlMega, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($curlMega, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($curlMega, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curlMega, CURLOPT_URL, $url);
//curl_setopt($curlMega, CURLOPT_POSTFIELDS, $toSend);
$result = curl_exec($curlMega);
curl_close($curlMega);
if (!is_null($result)){
    $filelog = __DIR__.'\log\control\\'.date('d_m_Y').'.log';
    $result .= date('d m Y H:i:s') . PHP_EOL;
    file_put_contents($filelog,$result,FILE_APPEND);
}


