<?php
class Log{

    public static function write($message, $source = LOG_GENERIC){
        $time = date('Y-m-d H:i:s');
        list($time_micros, ) = explode(" ", microtime());
        $time_micros *= 1000000;
        $ip = $_SERVER['REMOTE_ADDR'];
        $sk_hash = hash('sha256', Session::getSessionKey());

        DB::query('INSERT INTO '.TABLE_LOG.' (id, source, time, time_micros, ip, sessionkey_hash, message) VALUES (NULL, '.$source.', "'.$time.'", '.$time_micros.', "'.$ip.'", "'.$sk_hash.'", "'.$message.'")');
    }

    public static function read($sk_hash = ''){
        $entries = array();

        if($sk_hash != '') $where = ' WHERE sessionkey_hash = "'.$sk_hash.'"';
        else $where = '';

        $result = DB::query('SELECT * FROM '.TABLE_LOG.$where.' ORDER BY id DESC');

        for($i = 0; $i < $result->num_rows; $i++){
            $row = $result->fetch_assoc();
            $entries[] = new LogEntry($row['source'], $row['time'].'.'.$row['time_micros'], $row['ip'], $row['sessionkey_hash'], $row['message']);
        }
        return $entries;
    }

    public static function getSourceIcon($id){
        $icon = 'question-circle-o';
        $title = 'Nieznane pochodzenie';
        switch($id){
            case 0: $icon='info'; $title='Informacja'; break;
            case 1: $icon='unlock-alt'; $title='Logowanie'; break;
            case 2: $icon='users'; $title='Zarządzanie kontami'; break;
            case 3: $icon='shield'; $title='Administracja'; break;
            case 4: $icon='address-card-o'; $title='Menedżer sesji'; break;
        }
        return '<i class="cyan fa fa-fw fa-'.$icon.'" title="'.$title.'"></i>';
    }
}

class LogEntry{
    public $source;
    public $date;
    public $ip;
    public $sessionkey_hash;
    public $message;

    public function __construct($source, $date, $ip, $sk_hash, $message){
        $this->source = $source;
        $this->date = $date;
        $this->ip = $ip;
        $this->sessionkey_hash = $sk_hash;
        $this->message = $message;
    }
}
?>