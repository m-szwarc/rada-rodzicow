<?php
class Vote{
  public $user;
  public $question;
  public $answer;
  
  public function __construct($user, $question, $answer){
    $this->user = $user;
    $this->question = $question;
    $this->answer = $answer;
  }
  
  public static function getVotesForQuestion($id){
    $table = DB::query('SELECT * FROM '.TABLE_VOTES.' WHERE qid = '.$id.' ORDER BY id');
    $votes = array();
    for($i = 0; $i < $table->num_rows; $i++){
      $row = $table->fetch_assoc();
      $votes[] = new Vote($row['uid'], $row['qid'], $row['aid']);
    }
    return $votes;
  }

  public static function getVoteForQuestionByUser($qid, $uid){
    $table = DB::query('SELECT * FROM '.TABLE_VOTES.' WHERE qid = '.$qid.' AND uid = '.$uid);
    if($table->num_rows == 0) return false;
    $table = $table->fetch_assoc();
    return new Vote($table['uid'], $table['qid'], $table['aid']);
  }
}
?>