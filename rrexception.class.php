<?php
class RRException extends Exception{

    public function __construct($message, $code = 0){
        parent::__construct($message, $code);

        $uri = addslashes($_SERVER['REQUEST_URI']);
        $post = addslashes(self::parsePOST());

        DB::query('INSERT INTO '.TABLE_EXCEPTIONS.' (id, file, line, message, uri, post, datetime) VALUES (NULL, "'.addslashes($this->file).'", '.$this->line.', "'.$this->message.'", "'.$uri.'", "'.$post.'", FROM_UNIXTIME('.time().'))');
    }

    private static function parsePOST(){
        $output = '';
        foreach($_POST as $key => $value){
            $output.= $key.'='.$value.'&';
        }
        return rtrim($output, '&');
    }

    public static function getAll(){
        return ExceptionSubstitute::getAllExceptions();
    }
}

class ExceptionSubstitute{
    public $id;
    public $file;
    public $line;
    public $message;
    public $request_uri;
    public $post_data;
    public $date;

    public function __construct($id){
        $result = DB::query('SELECT * FROM '.TABLE_EXCEPTIONS.' WHERE id='.$id);
        if($result->num_rows == 0) throw new Exception('Exception with id '.$id.' doesn\'t exist.');

        $result = $result->fetch_assoc();
        $this->id = $result['id'];
        $this->file = $result['file'];
        $this->line = $result['line'];
        $this->message = $result['message'];
        $this->request_uri = $result['uri'];
        $this->post_data = $result['post'];
        $this->date = $result['datetime'];
    }

    public static function getAllExceptions(){
        $exceptions = array();

        $result = DB::query('SELECT * FROM '.TABLE_EXCEPTIONS.' ORDER BY id DESC');
        for($i = 0; $i < $result->num_rows; $i++){
            $row = $result->fetch_assoc();
            $exceptions[] = new ExceptionSubstitute($row['id']);
        }
        return $exceptions;
    }
}
?>