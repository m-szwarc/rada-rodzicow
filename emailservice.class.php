<?php
require('php-mailer/PHPMailerAutoload.php');

class EmailService{
    public static function sendEmail($to, $subject, $content){
        $mail = new PHPMailer();

        $mail->SMTPDebug = 0;                                 // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'marcinszwarc.mc';                  // SMTP username
        $mail->Password = 'qazmnb098';                        // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        $mail->CharSet = 'utf-8';

        $mail->setFrom('noreply-rada@szkola-poznan.salezjanie.pl', 'Portal Rady Rodziców ZSS');
        $mail->addAddress($to);                               // Add a recipient

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $result = $mail->send();
        if(!$result) try{ throw new RRException('Cannot send e-mail: '.$mail->ErrorInfo); }catch(Exception $e){};
        return $result;
    }
}
?>