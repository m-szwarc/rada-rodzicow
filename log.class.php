<?php
class Log{

    public static function write($message, $source = LOG_GENERIC){
        $time = date('Y-m-d H:i:s');
        $time_micros = (new DateTime())->format('u');
        $ip = $_SERVER['REMOTE_ADDR'];
        $sk_hash = hash('sha256', Session::getSessionKey());

        $res = DB::query('INSERT INTO '.TABLE_LOG.' (id, source, time, time_micros, ip, sessionkey_hash, message) VALUES (NULL, '.$source.', '.$time.', '.$time_micros.', "'.$ip.'", "'.$sk_hash.'", "'.$message.'")');
        if(!$res) echo('Logging failed!');
    }
}
?>