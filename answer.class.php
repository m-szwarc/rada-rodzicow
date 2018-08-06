<?php
class Answer{
  public $id;
  public $question;
  public $text;
  
  public function __construct($id, $question, $text){
    $this->id = $id;
    $this->question = $question;
    $this->text = $text;
  }

  public static function getAnswerById($id){
    $row = DB::query('SELECT * FROM '.TABLE_ANSWERS.' WHERE id = '.$id)->fetch_assoc();
    return new Answer($row['id'], $row['qid'], stripslashes($row['text']));
  }
  
  public static function getAnswersForQuestion($id){
    $table = DB::query('SELECT * FROM '.TABLE_ANSWERS.' WHERE qid = '.$id);
    $answers = array();
    for($i = 0; $i < $table->num_rows; $i++){
      $row = $table->fetch_assoc();
      $answers[] = new Answer($row['id'], $row['qid'], $row['text']);
    }
    return $answers;
  }
  
  public static function getTextForAnswer($id){
    $row = DB::query('SELECT * FROM '.TABLE_ANSWERS.' WHERE id = '.$id)->fetch_assoc();
    return trim(stripslashes($row['text']));
  }

  public static function create($question_id, $text){
    $text = addslashes($text);
    $r = DB::query('INSERT INTO '.TABLE_ANSWERS." (id, qid, text) VALUES (NULL, $question_id, '$text')");
    if(!$r) throw new RRExeption(DB::getError());
  }

  public static function setTextForAnswer($id, $text){
    $text = addslashes($text);
    $r = DB::query('UPDATE '.TABLE_ANSWERS." SET text='$text' WHERE id=$id");
    if(!$r) throw new RRExeption(DB::getError());
  }

  public static function deleteAnswer($id){
    $r = DB::query('DELETE FROM '.TABLE_ANSWERS." WHERE id=$id");
    if(!$r) throw new RRExeption(DB::getError());
    $r = DB::query('DELETE FROM '.TABLE_VOTES." WHERE aid=$id");
    if(!$r) throw new RRExeption(DB::getError());
  }

  public static function answerExistsForQuestion($answer_id, $question_id){
    return (DB::query('SELECT * FROM '.TABLE_ANSWERS.' WHERE id = '.$answer_id.' AND qid = '.$question_id)->num_rows > 0);
  }
}
?>