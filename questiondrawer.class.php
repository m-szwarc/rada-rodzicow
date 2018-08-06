<?php
class QuestionDrawer{
  public static function prepareCodeForForm($question){
    $code = '<div class="card">';
    if($question->hasAttachment()) $code.= '<i class="fa fa-link fa-fw right secondary" title="Do tego pytania dołączono plik(i)"></i>';
    $code.= '<big class="card-header no-border justify'.($question->hasAttachment() ? ' with-padding' : '').'">'.$question->text.'</big>';
    if($question->time_limit != 0){
      $code.= '<span class="secondary"><i class="fa fa-clock-o fa-fw" title="Limit czasu"></i> '.$question->getTimeLimitAsText().'</span>';
    }else{
      $code.= '<span class="secondary"><i class="fa fa-clock-o fa-fw"></i> Brak limitu czasu</span>';
    }

    $code.= '<div class="card-buttons">';
    $voted_id = $question->getAnswerForUser(Session::$current_user)->id;
    foreach($question->countVotes() as $answer_id => $count){
      if($question->checkUserHasVoted(Session::$current_user) || $question->isFinished() || $question->season != Season::getCurrent()){
        if($voted_id == $answer_id) $code.= '<button class="flat colored" type="button" disabled>'.Answer::getTextForAnswer($answer_id).' ('.$count.')</button>';
        else $code.= '<button class="flat" type="button" disabled>'.Answer::getTextForAnswer($answer_id).' ('.$count.')</button>';
      }else{
        $code.= '<button class="flat colored vote-button" type="button" data-qid="'.$question->id.'" data-aid="'.$answer_id.'" onclick="sendVote('.Session::$current_user->id.', '.$question->id.', '.$answer_id.')">';
        $code.= Answer::getTextForAnswer($answer_id).' (<span class="answer-votes" data-qid="'.$question->id.'" data-aid="'.$answer_id.'">'.$count.'</span>)</button>';
      }
    }
    $code.= '<a class="button flat right" href="view_question?question_id='.$question->id.'">Szczegóły</a>';
    $code.= '</div>';
    $code.= '</div>';
        
    return $code;
  }
  
  public static function prepareCodeForDetails($question){
    $code = '<div class="card">';
    $code.= '<big class="card-header no-border justify">'.$question->text.'</big>';
    if($question->time_limit != 0){
      $code.= '<span class="secondary block"><i class="fa fa-clock-o fa-fw" title="Limit czasu"></i> '.$question->getTimeLimitAsText().'</span>';
    }else{
      $code.= '<span class="secondary block"><i class="fa fa-clock-o fa-fw"></i> Brak limitu czasu</span>';
    }
    $code.= '<span class="secondary added-time"><i class="fa fa-pencil-square-o fa-fw" title="Data dodania"></i> '.$question->getDateAskedAsText().' r.</span>';
    $code.= self::prepareCodeForFiles($question);
    
    $code.= '<div class="card-buttons">';
    $voted_id = $question->getAnswerForUser(Session::$current_user)->id;
    foreach($question->answers as $answer){
      if($question->checkUserHasVoted(Session::$current_user) || $question->isFinished() || $question->season != Season::getCurrent()){
        if($voted_id == $answer->id) $code.= '<button class="flat colored" type="button" disabled>'.Answer::getTextForAnswer($answer->id).'</button>';
        else $code.= '<button class="flat" type="button" disabled>'.Answer::getTextForAnswer($answer->id).'</button>';
      }else{
        $code.= '<button class="flat colored vote-button" type="button" data-qid="'.$question->id.'" data-aid="'.$answer->id.'" onclick="sendVote('.Session::$current_user->id.', '.$question->id.', '.$answer->id.')">';
        $code.= Answer::getTextForAnswer($answer->id).'</button>';
      }
    }
    $code.= '</div>';
    $code.= '</div>';

    $code.= '<div class="card">';
    $code.= '<big class="card-header">Ilość głosów: <b class="total-votes" data-qid="'.$question->id.'">'.$question->countAllVotes().'</b></big>';

    $code.= '<ul>';
    foreach($question->getVotesByUsers() as $answer_id => $users){
      $code.= '<li>'.Answer::getTextForAnswer($answer_id).': ';
      $code.= '<b class="answer-votes" data-qid="'.$question->id.'" data-aid="'.$answer_id.'">'.count($users).'</b> osób';
      $code.= '<span class="full-results" style="display:none">';
      if(count($users) > 0){
        $code.= ' (';
        foreach($users as $key => $user){
          if($key > 0) $code.= ', ';
          $code.= $user->getFullName();
        }
        $code.= '<span class="new-vote" data-qid="'.$question->id.'" data-aid="'.$answer_id.'" style="display:none">, %name%</span>';
        $code.= ')';
      }else{
        $code.= '<span class="new-vote" data-qid="'.$question->id.'" data-aid="'.$answer_id.'" style="display:none"> (%name%)</span>';
      }
      $code.= '</span>';
      $code.= '</li>';
    }
    $code.= '</ul>';
    $code.= '<div class="card-buttons" id="show-full-results">';
    $code.= '<button class="flat" type="button" onclick="showFullResults()">';
    $code.= 'Pokaż szczegółowe wyniki</button>';
    $code.= '</div>';
    $code.= '</div>';

    if(Session::$current_user->id == $question->author->id){
      $code.= '<div class="card button-card">';
      $code.= '<a class="button flat colored" href="edit_question?question_id='.$question->id.'">Edytuj pytanie</a> ';
      $code.= '<a class="button flat colored" href="manage_files?question_id='.$question->id.'">Zarządzaj plikami</a> ';
      $code.= '<button class="button flat red" onclick="showDeleteToast('.$question->id.')">Usuń pytanie</button>';
      $code.= '</div>';
    }
    return $code;
  }

  public static function prepareCodeForFiles($question){
    $code = '';
    $images = array();
    $non_images = array();

    foreach($question->files as $file){
      $mime = getMimeTypeByFileName($file->getBaseName());
      if(strpos($mime, 'image/') !== false){
        $images[] = $file;
      }else{
        $non_images[] = $file;
      }
    }

    foreach($non_images as $file){
      $code.= '<div class="file-link">';
      $code.= '<span class="file-name">'.$file->getBaseName().'</span>';
      $code.= '<a href="files/'.$file->id.'">';
      $code.= '<i class="fa fa-download"></i> ';
      $code.= 'Pobierz</a>';
      $code.= '</div>';
    }
    $code.= '<div class="question-images">';
    foreach($images as $file){
      $code.= '<img class="question-image" src="files/'.$file->id.'" />';
    }
    $code.= '</div>';
    return $code;
  }
}
?>