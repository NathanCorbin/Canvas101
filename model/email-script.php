<?php
/**
 * Created by PhpStorm.
 * User: Shahbaz Iqbal
 * Date: 5/18/18
 * Time: 2:17 PM
 */



//    function email($email, $message)
//    {
//        $subject = "test";
//
//        $headers = 'From: webmaster@example.com' . "\r\n" .
//                    'Reply-To: webmaster@example.com' . "\r\n" .
//                    'X-Mailer: PHP/' . phpversion();
//
//        mail($email, $subject, $message, $headers);
//    }
//
//
//    function validEmail($email) {
//        return filter_var($email, FILTER_VALIDATE_EMAIL);
//    }
//
//    function validMessage($message) {
//        return strlen($message) > 0 && strlen($message) < 300;
//    }

require 'PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->setFrom('shabossis@hotmail.com', 'Shahbaz');
$mail->addAddress('ncorbin94@live.com', 'Mr. Nathan');
$mail->Subject  = 'We are Canvas 101';
$mail->Body     = 'Hi! This is my first e-mail sent through PHPMailer.';
if(!$mail->send()) {
    echo 'Message was not sent.';
    echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent.';
}