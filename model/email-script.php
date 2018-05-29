<?php
/**
 * Created by PhpStorm.
 * User: Shahbaz Iqbal
 * Date: 5/18/18
 * Time: 2:17 PM
 */ 
    
    require_once('/home/ncorbing/db.php');
    //require_once('/home/siqbalgr/database.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /**
     * Function that emails a student. Uses PHPMailer
     * for email access
     * 
     * @param $email the email address to send an email to
     * @param $message the email message to send
     * @param $subject the subject of the email
     * @return array that contains both arrays merged together
     */
    function email($email, $message, $subject) {
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

    /**
     * Validates the email address. Ensures that the email address
     * is in proper form
     * 
     * @return boolean true if valid, false if not
     */
    function validEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Checks if the message being sent isn't empty and that it
     * is under 300 characters
     * 
     * @return boolean true if greater than 0 and less than 300, false
     * if otherwise
     */
    function validMessage($message) {
        return strlen($message) > 0 && strlen($message) < 300;
    }
