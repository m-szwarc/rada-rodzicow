<?php
class LogInService{
    public static $lastLoginResult = -1;
    public static $loggedInUser = null;

    public static function isUserAuthorized($user){
        return ($user->id != 0 && $user->priv != 0);
    }
    
    public static function tryToLogIn($login, $pass){
        $pass = hash('sha256', $pass);
        $users = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE login = "'.$login.'" AND pass = "'.$pass.'" AND priv != 0');
        if($users->num_rows == 0){
            self::$lastLoginResult = 0;
            self::$loggedInUser = null;
            self::saveState($login);
            return;
        }
        for($i=0; $i<$users->num_rows; $i++){
            $user = $users->fetch_assoc();
            if(strtolower($user['login']) == strtolower($login) && $user['pass'] == $pass && $user['priv'] != 0){
                self::$loggedInUser = new User($user['id']);
                if(self::$loggedInUser->checkPriv(PRIV_LOGIN)){
                    self::$lastLoginResult = 1;
                    self::saveState($login);
                    return;
                }else{
                    self::$lastLoginResult = 2;
                    self::$loggedInUser = null;
                    self::saveState($login);
                    return;
                }
            }else{
                self::$lastLoginResult = 0;
                self::$loggedInUser = null;
                self::saveState($login);
            }
        }
    }

    private static function saveState($login){
        $result = self::$lastLoginResult;
        switch($result){
            case 1: Log::write('Successful login attempt ('.$login.')', LOG_LOGIN); break;
            case 2: Log::write('Blocked login attempt ('.$login.')', LOG_LOGIN); break;
            default: Log::write('Unsuccessful login attempt ('.$login.')', LOG_LOGIN); break;
        }
        if(!DB::query('INSERT INTO '.TABLE_LOGIN_HISTORY." (id, datetime, login, ip, result) VALUES (NULL, FROM_UNIXTIME(".time()."), '$login', '".$_SERVER['REMOTE_ADDR']."', $result)")){
            try{
                throw new RRException(DB::getError());
            }catch(Exception $e){

            }
        }
    }

    public static function getLoginHistory(){
        $entries = array();

        $result = DB::query('SELECT * FROM '.TABLE_LOGIN_HISTORY.' ORDER BY id DESC');
        for($i = 0; $i < $result->num_rows; $i++){
            $row = $result->fetch_assoc();
            $entries[] = new LoginHistoryEntry($row['datetime'], $row['login'], $row['ip'], $row['result']);
        }
        return $entries;
    }

    public static function getIconForResult($res){
        switch($res){
            case 1:
                $icon = 'check';
                $description = 'Zalogowano pomyślnie';
                $color = 'green';
            break;
            case 2:
                $icon = 'lock';
                $description = 'Konto zablokowane';
                $color = 'secondary';
            break;
            default:
                $icon = 'times';
                $description = 'Nie zalogowano';
                $color = 'red';
        }

        $code = '<i class="fa fa-'.$icon.' '.$color.' fa-fw" title="'.$description.'"></i>';
        return $code;
    }
}

class LoginHistoryEntry{
    public $date;
    public $login;
    public $ip;
    public $result;

    public function __construct($date, $login, $ip, $result){
        $this->date = $date;
        $this->login = $login;
        $this->ip = $ip;
        $this->result = $result;
    }
}
?>