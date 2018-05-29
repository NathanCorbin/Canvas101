<?php
/**
 * Created by PhpStorm.
 * User: Shahbaz Iqbal
 * Date: 5/18/18
 * Time: 2:17 PM
 */

//require_once('/home/ncorbing/db.php');
require_once('/home/siqbalgr/database.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function email($email, $message, $subject)
{
    new UserDB();
    $mail = new PHPMailer(true);

    try {

        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = UserDB::getEmailHost();
        $mail->SMTPAuth = true;
        $mail->Username = UserDB::getEmailAddress();
        $mail->Password = UserDB::getEmailPassword();
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom(UserDB::getEmailAddress(), 'Green River');
        $mail->addAddress($email);
        $mail->addReplyTo(UserDB::getEmailAddress());

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $message;

        $mail->send();

    } catch (Exception $e) {
        echo '<p>Message could not be sent. Mailer Error: ', $mail->ErrorInfo."</p>";
    }
}

function validEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validMessage($message) {
    return strlen($message) > 0 && strlen($message) < 300;
}