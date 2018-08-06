<?php
class PasswordResetService{
    public static $lastState = -1;

    public static function tryToResetPassword($login, $email){
        self::$lastState = 0;
        try{
            $user = User::getUserByLogin($login);
        }catch(Exception $e){
            return;
        }

        if($user->email == $email){
            self::$lastState = -2; // Nie udało się wysłać wiadomości
            $newpass = self::generatePassword();
            if(self::sendPasswordEmail($email, $user, $newpass)){
                self::$lastState = 1;
                $user->changePassword($newpass);
            }
        }
    }

    private static function sendPasswordEmail($email, $user, $newpass){
        $body = file_get_contents('password_reset_mail_template.html');
        $body = str_replace('%fname%', $user->first_name, $body);
        $body = str_replace('%lname%', $user->last_name, $body);
        $body = str_replace('%pass%', $newpass, $body);
        return EmailService::sendEmail($email, 'Odzyskiwanie hasła', $body);
    }

    private static function generatePassword(){
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[scrand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
?>