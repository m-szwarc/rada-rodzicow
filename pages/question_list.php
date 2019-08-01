<?php
$season = Season::getCurrent();
if(isset($_GET['season'])) $season = $_GET['season'];

if(!Session::$current_user->checkAccessForSeason($season)){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Brak dostępu</h1>');
    echo('  Nie masz dostępu do głosowań z roku '.Season::getSchoolyearForSeason($season));
    echo('</div>');
    goto after_question_list;
}
$questions = Question::getQuestionsForSeason($season);

if($season == Season::getCurrent()){
    $already_voted = array();
    $not_voted_yet = array();
    $finished = array();

    foreach($questions as $question){
        if($question->isFinished()) $finished[] = $question;
        else if($question->checkUserHasVoted(Session::$current_user)) $already_voted[] = $question;
        else $not_voted_yet[] = $question;
    }

    echo('<h1 class="center">Głosowania aktualne</h1>');
    foreach($not_voted_yet as $question){
        echo(QuestionDrawer::prepareCodeForForm($question));
    }
    if(count($not_voted_yet) == 0) echo('<i class="block center secondary">Brak głosowań</i>');

    $i = 0;
    echo('<h1 class="center">Nieaktywne</h1>');
    foreach($already_voted as $question){
        if($i == 5) echo('<div class="hidden" id="more-questions-1">');
        echo(QuestionDrawer::prepareCodeForForm($question));
        $i++;
    }
    if($i >= 5){
        echo('</div>');
        echo('<div class="center"><a class="no-underline" href="javascript:void()" onclick="showQuestions(this, \'more-questions-1\')"><i class="fa fa-chevron-circle-down"></i> Pokaż więcej głosowań ('.($i-5).')</a></div>');
    }
    if(count($already_voted) == 0) echo('<i class="block center secondary">Brak głosowań</i>');

    $i = 0;
    echo('<h1 class="center">Zakończone</h1>');
    foreach($finished as $question){
        if($i == 5) echo('<div class="hidden" id="more-questions-2">');
        echo(QuestionDrawer::prepareCodeForForm($question));
        $i++;
    }
    if($i >= 5){
        echo('</div>');
        echo('<div class="center"><a class="no-underline" href="javascript:void()" onclick="showQuestions(this, \'more-questions-2\')"><i class="fa fa-chevron-circle-down"></i> Pokaż więcej głosowań ('.($i-5).')</a></div>');
    }
    if(count($finished) == 0) echo('<i class="block center secondary">Brak głosowań</i>');
}else{
    echo('<h1 class="center">Archiwum głosowań z roku '.Season::getSchoolyearForSeason($season).'</h1>');
    foreach($questions as $question){
        echo(QuestionDrawer::prepareCodeForForm($question));
    }
    if(count($questions) == 0) echo('<i class="block center secondary">Brak głosowań</i>');
}

after_question_list:
if((Session::$current_user->season & ~(1 << (Season::getCurrent() - 1))) == 0) return;
?>
<hr />
<div class="card">
    <h1 class="card-header">Archiwum głosowań</h1>
    Przejdź do archiwum z roku:
    <ul>
        <?php
            for($j = Season::getCurrent() + 1; $j <= Season::getCurrent() + 32; $j++){
                $i = $j % 32;
                if(Season::isFutureSeason($i)) continue;
                if(Season::getYearForSeason($i) <= 2014) continue;
                if(!Session::$current_user->checkAccessForSeason($i)) continue;

                if($i == Season::getCurrent()){
                    if($season == $i) continue;
                    else{
                        echo('<li><a href="question_list?season='.$i.'">Przejdź do głosowań bieżących</a></li>');
                        continue;
                    }
                }
                
                echo('<li><a href="question_list?season='.$i.'">'.Season::getSchoolyearForSeason($i).'</a></li>');
            }
        ?>
    </ul>
</div>