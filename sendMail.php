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
//require '../vendor/autoload.php';
//require 'handler.php';
// ---------------------------------------------
//require 'PHPMailer/PHPMailer.php';
//require 'PHPMailer/SMTP.php';
//require 'PHPMailer/Exception.php';
require 'module/libmail.php';
// ---------------------------------------------
// ----- todo імя файлу має формуватися унікальним і передаватися параметром
// ----- todo повернути повідомлення про помилки або про успішне виконання
$email_errors = [];

$data_post = json_decode($_POST['data'],true);

foreach ($data_post['files'] as $file) {
    $data_file = file_get_contents($data_post['folder'] . $file);
    if (!$data_file) {
        $email_errors[] = ['message' => 'error reading file' , 'file' => $data_post['folder'] . $file];
        continue;
    }
	$pos = strpos($data_file, '{');
	$data_file = substr($data_file, $pos);
    $data_file = mb_convert_encoding($data_file, "UTF-8", "CP866");


    $data_file = json_decode($data_file, true);

    if (json_last_error() !== 0) {
        $email_errors[] = ['message' => 'error json_decode file' , 'file' => $data_post['folder'] . $file];
        continue;
    }

    $result_xml = create_xml($data_file);

    if (!$result_xml){
        $email_errors[] = ['message' => 'error create xml file' , 'file' => $data_post['folder'] . $file];
        continue;
    }

    $result_email = send_email($data_file['FileName']);

    unlink('email/' . $data_file['FileName']);

    if ($result_email !== true) {
        $email_errors[] = ['message' => $result_email , 'file' => $data_post['folder'] . $file];
        continue;
    }

}
echo json_encode($email_errors);

exit;
/**
 * @param array $data_file
 * @return string
 */
function create_xml($data_file)
{
    // ------- create xml document ---------- //
    $dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
    $root = $dom->createElement("Event"); // Создаём корневой элемент
    $root->setAttribute("event", "PC");
    $dom->appendChild($root);
    $user = $dom->createElement("Ident","11");

    $user->setAttribute("ByOperator", $data_file['ByOperator']);
    $user->setAttribute("ByOperatorName", $data_file['ByOperatorName']);
    $user->setAttribute("ByStationCode", $data_file['ByStationCode']);
    $user->setAttribute("ByStationName", $data_file['ByStationName']);
    $user->setAttribute("ByService", $data_file['ByService']);
    $user->setAttribute("CarrierCode", $data_file['CarrierCode']);
    $user->setAttribute("CarrierName", $data_file['CarrierName']);
    $user->setAttribute("DepartureDate", $data_file['DepartureDate']);
    $user->setAttribute("DepartureTime", $data_file['DepartureTime']);

    $user->setAttribute("FileName", $data_file['FileName']);
    $user->setAttribute("Group",  $data_file['Group']);
    $user->setAttribute("LinkStationCode", $data_file['LinkStationCode']);
    $user->setAttribute("NameRoute", $data_file['NameRoute']);
    $user->setAttribute("NumberRoute", $data_file['NumberRoute']);
    $user->setAttribute("IdRoute", $data_file['IdRoute']);
    $user->setAttribute("Stamp", "");
    $user->setAttribute("TimeStamp", $data_file['TimeStamp'] );
    $user->setAttribute("ConnectionCode", "");
    $user->setAttribute("event", $data_file['event']);

    $root->appendChild($user);
    // ----- завершили создание документа. Далее проверим его на ошибки
    $data = $dom->saveXML();
    $parser = xml_parser_create();
    xml_parse($parser, $data, true);
    $error_code = xml_get_error_code($parser);
    xml_parser_free($parser);
    if ($error_code !== 0){
        return false;
    }
    // ----- запишем в файл
    $dom->save('email/' . $data_file['FileName']); // Сохраняем полученный XML-документ в файл
    return true;
}
/**
 * @return  true or error message
 */
function send_email($file)
{
    // 3hkOcgWl2Nst3gCl  vatvopas
    // el9194NyiOVPisro voldiner
    if ( !file_exists('email/' . $file)){
        return 'error - file not exist: email/' . $file;
    }
    // ------------------- phpmailer есть возможность получать ошибки
   /* //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        //$mail->isSMTP();                                            //Send using SMTP
        //$mail->Host       = 'smtp.ukr.net';                         //Set the SMTP server to send through
        //$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        //$mail->Username   = 'voldiner@ukr.net';                     //SMTP username
        //$mail->Password   = 'el9194NyiOVPisro';                     //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit SSL encryption
        //$mail->Port       = 2525;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('vatvopas@ukr.net', 'vopas');
        $mail->addAddress('vold@vopas.com.ua', 'voldiner');     //Add a recipient

        //Attachments
        $mail->addAttachment('email/' . $file);         //Add attachments
        //Content
        //$mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'cancellation message from prat vopas';
        $mail->Body    = 'cancellation message from prat vopas';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }*/
    // ----------------- libmail
    $subject = 'cancellation message from prat vopas';
    $userMail = 'vold@vopas.com.ua';
    $m = new Mail("utf-8");
    //$m->autoCheck(false);
    $m->From('vatvopas@ukr.net');
    $m->To($userMail);
    $m->Subject($subject);
    $m->Body("cancellation message from prat vopas");
    $m->Cc( "info@vopas.com.ua");  // кому отправить копию письма
    $m->Attach( 'email/' . $file) ;
    $m->Priority(3);
    $m->Send();
    // --------------
    return true;
}



