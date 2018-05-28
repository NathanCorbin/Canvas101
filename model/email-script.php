<?php
/**
 * Created by PhpStorm.
 * User: Shahbaz Iqbal
 * Date: 5/18/18
 * Time: 2:17 PM
 */



    function email($email, $message)
    {
        $subject = "test";

        $headers = 'From: webmaster@example.com' . "\r\n" .
                    'Reply-To: webmaster@example.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

        mail($email, $subject, $message, $headers);
    }


    function validEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validMessage($message) {
        return strlen($message) > 0 && strlen($message) < 300;
    }