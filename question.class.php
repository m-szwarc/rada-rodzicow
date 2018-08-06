<?php
class Question{
    public $id;
    public $text;
    public $answers;
    public $files;
    public $author;
    public $time_asked;
    public $time_limit;
    public $season;
    
    public function __construct($id){
        $qst_row = DB::query('SELECT * FROM '.TABLE_QUESTIONS.' WHERE id = '.$id);
        if($qst_row->num_rows == 0) throw new RRException("Question with id $id doesn't exist");
        $qst_row = $qst_row->fetch_assoc();

        $files_sql = DB::query('SELECT * FROM '.TABLE_FILES.' WHERE related_question = '.$id);
        $files = array();
        for($i = 0; $i < $files_sql->num_rows; $i++){
            $file_row = $files_sql->fetch_assoc();
            $files[] = new File($file_row['id']);
        }

        $this->id = $qst_row['id'];
        $this->text = stripslashes($qst_row['text']);
        $this->answers = Answer::getAnswersForQuestion($this->id);
        $this->files = $files;
        $this->author = new User($qst_row['author']);
        $this->time_asked = strtotime($qst_row['date']);
        $this->time_limit = strtotime($qst_row['time_max']);
        $this->season = $qst_row['season'];
    }
    
    public function change($text, $time_limit){
        $time_limit = date('Y-m-d H:i:s', $time_limit);
        $r = DB::query('UPDATE '.TABLE_QUESTIONS." SET text = '$text', time_max = '$time_limit' WHERE id = ".$this->id);
        if(!$r) throw new RRException(DB::getError());
    }
    
    public function getVotes(){
        return Vote::getVotesForQuestion($this->id);
    }
    
    public function countAllVotes(){
        return count($this->getVotes());
    }
    
    public function countVotes(){
        $votes = $this->getVotes();
        $count = array();
        foreach($this->answers as $answer){
        $count[$answer->id] = 0;
        }
        foreach($votes as $vote){
        $count[$vote->answer]++;
        }
        return $count;
    }
    
    public function getTimeLimitAsText(){
        return date('d.m.Y G:i', $this->time_limit);
    }
    
    public function getDateAskedAsText(){
        return date('d.m.Y', $this->time_asked);
    }
    
    public function getVotesByUsers(){
        $votes = $this->getVotes();
        $list = array();
        foreach($this->answers as $answer){
            $list[$answer->id] = array();
        }
        foreach($votes as $vote){
            $list[$vote->answer][] = new User($vote->user);
        }
        return $list;
    }
    
    public static function getQuestionsForSeason($season){
        $questions = DB::query('SELECT * FROM '.TABLE_QUESTIONS.' WHERE season = '.$season.' ORDER BY id DESC');
        $list = array();
        for($i = 0; $i < $questions->num_rows; $i++){
            $question = $questions->fetch_assoc();
            $list[] = new Question($question['id']);
        }
        return $list;
    }
    
    public function checkUserHasVoted($user){
        $votes = $this->getVotes();
        foreach($votes as $vote){
            if($vote->user == $user->id) return true;
        }
        return false;
    }
    
    public function isFinished(){
        return ($this->time_limit < time() && $this->time_limit > 0);
    }
    
    public function hasAttachment(){
        return count($this->files) > 0;
    }

    public function getAnswerForUser($user){
        $result = Vote::getVoteForQuestionByUser($this->id, $user->id);
        if($result === false) return new Answer(0, 0, '');
        else return Answer::getAnswerById($result->answer);
    }

    public static function create($text, $answers, $time_limit, $author){
        $added_date = date('Y-m-d H:i:s');
        $time_limit = date('Y-m-d H:i:s', $time_limit);
        $season = Season::getCurrent();
        $uid = $author->id;
        $r = DB::query('INSERT INTO '.TABLE_QUESTIONS." (id, text, date, time_max, season, author) VALUES (NULL, '$text', '$added_date', '$time_limit', $season, $uid)");
        if(!$r) throw new RRException(DB::getError());

        $row = DB::query('SELECT * FROM '.TABLE_QUESTIONS.' ORDER BY id DESC')->fetch_assoc();
        $id = $row['id'];

        foreach($answers as $answer){
        Answer::create($id, trim($answer));
        }

        return $id;
    }

    public static function delete($id){
        $r = DB::query('DELETE FROM '.TABLE_QUESTIONS.' WHERE id='.$id);
        if(!$r) throw new RRException(DB::getError());
    }

    public function vote($user_id, $answer_id){
        return DB::query('INSERT INTO '.TABLE_VOTES." (id, uid, qid, aid) VALUES (NULL, $user_id, ".$this->id.", $answer_id)");
    }
}
?>