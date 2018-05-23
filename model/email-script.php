<?php
/**
 * Created by PhpStorm.
 * User: Shahbaz Iqbal
 * Date: 5/18/18
 * Time: 2:17 PM
 */


    $email=$_POST['email'];
    $subject = 'Your subject for email';
    $message = 'Body of your message';

    mail($email, $subject, $message);
?>