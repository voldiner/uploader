<?php
/**
 * відправка поштового повідомлення про скасування рейсу
 * 1.  Викликається post запитом в якому знаходяться всі необхідні для формування
 * листа дані
 * 2. формується xml обєкт відповідної структури
 * 3. відправляється лист з допомогою бібліотеки ------???
 *  return false or true
 */
//ini_set('error_reporting', 0);
//ini_set('display_errors', 0);

error_reporting(E_ALL);
//--------- для отладки ------------------------
require '../vendor/autoload.php';
require 'handler.php';
// ---------------------------------------------
include 'module/libmail.php';
// ---------------------------------------------
// ----- todo імя файлу має формуватися унікальним і передаватися параметром
// ----- todo повернути повідомлення про помилки або про успішне виконання
$email_errors = [];
$data_post = json_decode($_POST['data'],true);
/*$data_post = [
    'files' => ['email.txt'],
    'folder' => 'f:\vopas\email\\',
];*/


foreach ($data_post['files'] as $file) {
    $data_file = file_get_contents($data_post['folder'] . $file);
    if (!$data_file) {
        $email_errors[] = ['message' => 'error reading file' , 'file' => $data_post['folder'] . $file];
        continue;
    }

    $data_file = mb_convert_encoding($data_file, "UTF-8", "CP866");

    $data_file = json_decode($data_file, true);

    if (json_last_error() !== 0) {
        $email_errors[] = ['message' => 'error json_decode file' , 'file' => $data_post['folder'] . $file];
        continue;
    }

    $data_xml = create_xml($data_file);

    send_email($data_xml);

}
echo json_encode($email_errors);

exit;
/**
 * @param $data_file
 * @return string
 */
function create_xml($data_file)
{
    // ------- create xml document ---------- //
    $dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
    $root = $dom->createElement("Event"); // Создаём корневой элемент
    $root->setAttribute("event", "PC");
    $dom->appendChild($root);
    $user = $dom->createElement("Ident", $data_file['content']);

    $user->setAttribute("ByOperator", $data_file['ByOperator']);
    $user->setAttribute("ByOperatorName", $data_file['ByOperatorName']);
    $user->setAttribute("ByStationCode", $data_file['ByOperatorName']);
    $user->setAttribute("ByStationName", $data_file['ByStationName']);
    $user->setAttribute("ByService", $data_file['ByService']);
    $user->setAttribute("ByService", "vopas");
    $user->setAttribute("CarrierCode", $data_file['CarrierCode']);
    $user->setAttribute("CarrierName", $data_file['CarrierName']);
    $user->setAttribute("FileName", "");
    $user->setAttribute("Group", "");
    $user->setAttribute("LinkStationCode", "");
    $user->setAttribute("NameRoute", $data_file['NameRoute']);
    $user->setAttribute("NumberRoute", $data_file['NumberRoute']);
    $user->setAttribute("IdRoute", $data_file['IdRoute']);
    $user->setAttribute("Platform", "");
    $user->setAttribute("Stamp", "");
    $user->setAttribute("TimeStamp", date('Y-m-d H:i:s'));
    $user->setAttribute("ConnectionCode", "");
    $user->setAttribute("event", "PC");

    $root->appendChild($user);

    //$dom->save("users.xml"); // Сохраняем полученный XML-документ в файл
    return $dom->saveXML();

}


function send_email($data_xml)
{
    $subject = 'Повідомлення про скасування рейсу';
    $userMail = 'vold@vopas.com.ua';
    $m = new Mail("utf-8");
    //$m->autoCheck(false);
    $m->From('voldiner@ukr.net');
    $m->To($userMail);
    $m->Subject($subject);
    $m->Body($data_xml, "text");
    $m->Priority(3);
    $m->Send();

}



