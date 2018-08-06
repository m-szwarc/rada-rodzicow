<?php
require('db_cred.php');
class DB{
  private static $host = DB_HOST;
  private static $user = DB_USER;
  private static $pass = DB_PASS;
  private static $base = DB_BASE;
  private static $connector;
  
  public static function connect(){
    self::$connector = new mysqli(self::$host, self::$user, self::$pass, self::$base);
  }
  
  public static function query($query){
    return self::$connector->query($query);
  }
  
  public static function getConnector(){
    return self::$connector;
  }
  
  public static function close(){
    self::$connector->close();
  }

  public static function getError(){
    return self::$connector->error;
  }
}
?>