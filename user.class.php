<?php
class User{
    public $id = 0;
    public $login = null;
    private $pass = null;
    public $first_name = null;
    public $last_name = null;
    public $priv = 0;
    public $creation_time = 0;
    public $email = null;
    public $appendix = null;
    public $season = 0;
    
    public function __construct($id){
        if($id == 0) return;
        $user_row = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE id = '.$id);
        if($user_row->num_rows == 0) throw new RRException("User with id $id doesn't exist.");
        $user_row = $user_row->fetch_assoc();
        $this->id = $user_row['id'];
        $this->login = $user_row['login'];
        $this->pass = $user_row['pass'];
        $this->first_name = $user_row['imie'];
        $this->last_name = $user_row['nazwisko'];
        $this->priv = $user_row['priv'];
        $this->creation_time = strtotime($user_row['creation_time']);
        $this->email = $user_row['email'];
        $this->appendix = $user_row['appendix'];
        $this->season = $user_row['season'];
    }
    
    public function getFullName(){
        return $this->first_name.' '.$this->last_name;
    }
    
    public function changePassword($new_pass){
        if(!DB::query('UPDATE '.TABLE_USERS.' SET pass = "'.hash('sha256', $new_pass).'" WHERE id='.$this->id)) throw new RRException(DB::getError());
    }
    
    public function changeEmail($new_email){
        if(!DB::query('UPDATE '.TABLE_USERS.' SET email = "'.$new_email.'" WHERE id='.$this->id)) throw new RRException(DB::getError());
    }
    
    public function checkAccessForSeason($season){
        if(Season::getYearForSeason($season) > date('Y', $this->creation_time) + 22) return false;
        return ((1 << ($season - 1)) & $this->season) != 0;
    }
    
    public function checkPriv($priv){
        return (($this->priv & $priv) != 0);
    }
    
    public static function getEmpty(){
        return new User(0);
    }

    public static function getAll(){
        $users = array();
        $sql_users = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE priv != 0 ORDER BY SIGN(priv) ASC, nazwisko ASC, imie ASC');
        if(!$sql_users) throw new RRException(DB::getError());
        for($i = 0; $i < $sql_users->num_rows; $i++){
        $user = $sql_users->fetch_assoc();
        $users[] = new User($user['id']);
        }
        return $users;
    }

    public static function getDeleted(){
        $users = array();
        $sql_users = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE priv = 0 ORDER BY nazwisko ASC, imie ASC');
        if(!$sql_users) throw new RRException(DB::getError());
        for($i = 0; $i < $sql_users->num_rows; $i++){
        $user = $sql_users->fetch_assoc();
        $users[] = new User($user['id']);
        }
        return $users;
    }

    public function checkPassword($pass_hash){
        return $this->pass == $pass_hash;
    }

    public static function create($first_name, $last_name, $login, $pass, $email){
        if(!is_null($email)) $email = "'".$email."'"; else $email = 'NULL';
        DB::query('INSERT INTO '.TABLE_USERS." (login, pass, imie, nazwisko, priv, creation_time, email, season) VALUES ('$login', '".hash('sha256', $pass)."', '$first_name', '$last_name', 1, FROM_UNIXTIME(".time()."), $email, ".(1 << (Season::getCurrent() - 1)).")");
    }

    public static function loginExists($login){
        $r = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE login = "'.$login.'"');
        if($r == false) return false;
        if($r->num_rows == 0) return false;
        return true;
    }

    public static function getUserByLogin($login){
        $r = DB::query('SELECT * FROM '.TABLE_USERS.' WHERE login = "'.$login.'"');
        if($r == false) throw new RRException('User with login '.$login.' doesn\'t exist.');
        if($r->num_rows == 0) throw new RRException('User with login '.$login.' doesn\'t exist.');
        $r = $r->fetch_assoc();
        return new User($r['id']);
    }

    public function changeName($first, $last){
        if(!DB::query('UPDATE '.TABLE_USERS.' SET imie="'.$first.'", nazwisko="'.$last.'" WHERE id='.$this->id))
        throw new RRException(DB::getError());
    }

    public function setSeason($season){
        if(!DB::query('UPDATE '.TABLE_USERS.' SET season='.$season.' WHERE id='.$this->id))
        throw new RRException(DB::getError());
    }

    public function setPriv($priv){
        if(!DB::query('UPDATE '.TABLE_USERS.' SET priv='.$priv.' WHERE id='.$this->id))
        throw new RRException(DB::getError());
    }

    public function delete(){
        $this->setPriv(0);
    }

    public function restore(){
        $this->setPriv(1);
    }
}
?>