<?php

//echo (extension_loaded('openssl')?'SSL loaded':'SSL not loaded')."\n";
require_once 'PHPMailerAutoload.php';
function sendMail($subject, $body, $arr_address, $arr_cc) {
//    $arr_cc = $arr_address = array();
//    $arr_address['mohamed.mahmoud@business-telecoms.com'] = 'Mohamed Mahmoud';
//    $arr_cc['ahmedou@business-telecoms.com'] = 'Ahmedou Balla';
//    $arr_cc['abeidi@business-telecoms.com'] = 'Yacoub Abeidi';
    $mail = new PHPMailer;
    $mail->isSMTP();
    foreach ($arr_address as $add => $name)
        $mail->addAddress($add, $name);
    foreach ($arr_cc as $add => $name)
        $mail->addCC($add, $name);

    $mail->addReplyTo('timiris@mattel.mr', 'TIMRIS Plateforme');
//$mail->addBCC('bcc@example.com');
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    $mail->isHTML(true);                                  // Set email format to HTML
    if (isset($subject))
        $mail->Subject = $subject;
    if (isset($body))
        $mail->Body = $body;
    $ret = array();
    if (!$mail->send()) {
        $ret['send'] = 0;
        $ret['message'] = "\r\n" . $mail->ErrorInfo;
    }
    else{
        $ret['send'] = 1;
        $ret['message'] = "";
    }
    return $ret;
}

?>