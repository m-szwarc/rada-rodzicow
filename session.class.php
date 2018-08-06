<?php
include('random-compat/random.php');    // Random number generator for PHP 5
                                        // Random Compatibility Library by Paragon Initiative Enterprises

class Session{
  public static $current_user;
  static $key;
  static $id;
  static $data;
  
  public static function start($key = null){
    $given_key = true;

    if(is_null($key)){
      $given_key = false;
      if(!isset($_COOKIE['SESSION'])){
        self::$key = self::generateKey();
      }else{
        self::$key = $_COOKIE['SESSION'];
      }
      $key = self::$key;
    }else self::$key = $key;

    $result = DB::query('SELECT * FROM '.TABLE_SESSIONS." WHERE session_key='$key'");
    if(!$result) throw new RRException('Nie można rozpocząć sesji: '.DB::getError());

    $result = $result->fetch_assoc();
    self::$id = $result['id'];

    // Utwórz nową sesję zamiast przestarzałej
    if(strtotime($result['expire_date']) < time()){
      if($given_key){
        throw new RRException('Podana sesja nie istnieje.');
      }

      self::$key = self::generateKey();
      $key = self::$key;

      $result = DB::query('SELECT * FROM '.TABLE_SESSIONS." WHERE session_key='$key'");
      if(!$result) throw new RRException('Nie można rozpocząć sesji: '.DB::getError());

      $result = $result->fetch_assoc();
      self::$id = $result['id'];
    }

    $result = DB::query('SELECT * FROM '.TABLE_SESSION_DATA.' WHERE session_id='.self::$id.' ORDER BY id ASC');
    if(!$result) throw new RRException('Nie można załadować danych sesji.');
    for($i=0; $i<$result->num_rows; $i++){
      $row = $result->fetch_assoc();
      self::$data[$row['key']] = $row['value'];
    } 
  }

  public static function getSessionKey(){
    return self::$key;
  }
  
  public static function get($key){
    if(!isset(self::$data[$key])) return null;
    return self::$data[$key];
  }
  
  public static function set($key, $value){
    self::$data[$key] = $value;
    $result = DB::query('INSERT INTO '.TABLE_SESSION_DATA.' (id, session_id, `key`, `value`) VALUES (NULL, '.self::$id.', "'.$key.'", "'.$value.'")');
    if(!$result) throw new RRException('Nie można zapisać danych sesji: '.DB::getError());
  }
  
  public static function restoreUser(){
    $uid = self::get('uid');
    if($uid === null) self::$current_user = User::getEmpty();
    else self::$current_user = new User($uid);
  }
  
  public static function setCurrentUser($user){
    self::$current_user = $user;
    self::set('uid', $user->id);
  }
  
  public static function logUserOut(){
    self::$current_user = User::getEmpty();
    self::set('uid', 0);
  }

  static function generateKey(){
    $unique = false;
    $key = '';
    while(!$unique){
      $key = self::getRandomKey();
      $res = DB::query('SELECT * FROM '.TABLE_SESSIONS.' WHERE session_key = "'.$key.'"');
      if($res->num_rows == 0) $unique = true;
    }
    DB::query('INSERT INTO '.TABLE_SESSIONS.' (id, session_key, expire_date) VALUES (NULL, "'.$key.'", FROM_UNIXTIME('.(time()+3600).'))');
    setcookie('SESSION', $key);
    return $key;
  }

  static function getRandomKey(){
    if(!function_exists('random_bytes')) return randomStr(64);
    try{
      $string = bin2hex(random_bytes(32));
    }catch(Exception $e){
      $string = randomStr(64);
    }
    return $string;
  }

  static function randomStr($length){
    $str = '';
    $keyspace = '0123456789abcdef';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for($i = 0; $i < $length; $i++){
      $str .= $keyspace[rand(0, $max)];
    }
    return $str;
  }

  static function renew(){
    DB::query('UPDATE '.TABLE_SESSIONS.' SET expire_date = FROM_UNIXTIME('.(time()+3600).') WHERE id='.self::$id);
  }
}
?>