<?php
class Log{

    public static function write($message, $source = LOG_GENERIC){
        $time = date('Y-m-d H:i:s');
        list($time_micros, ) = explode(" ", microtime());
        $time_micros *= 1000000;
        $ip = $_SERVER['REMOTE_ADDR'];
        $sk_hash = hash('sha256', Session::getSessionKey());

        $res = DB::query('INSERT INTO '.TABLE_LOG.' (id, source, time, time_micros, ip, sessionkey_hash, message) VALUES (NULL, '.$source.', "'.$time.'", '.$time_micros.', "'.$ip.'", "'.$sk_hash.'", "'.$message.'")');
        //if(!$res) echo('Logging failed! '.DB::getError());
    }

    public static function read(){
        $entries = array();
        $result = DB::query('SELECT * FROM '.TABLE_LOG.' ORDER BY id DESC');

        for($i = 0; $i < $result->num_rows; $i++){
            $row = $result->fetch_assoc();
            $entries[] = new LogEntry($row['source'], $row['time'].'.'.$row['time_micros'], $row['ip'], $row['sessionkey_hash'], $row['message']);
        }
        return $entries;
    }

    public static function getSourceIcon($id){
        $icon = '';
        $title = '';
        switch($id){
            case 0: $icon='info'; $title='Informacja'; break;
            case 1: $icon='unlock-alt'; $title='Logowanie'; break;
            case 2: $icon='users'; $title='Zarządzanie kontami'; break;
            case 3: $icon='shield'; $title='Administracja'; break;
            default: $icon='question-circle-o'; $title='Nieznane pochodzenie'; break;
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