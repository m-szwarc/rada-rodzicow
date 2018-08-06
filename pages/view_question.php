<?php
if(!isset($_GET['question_id'])){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie został podany numer pytania.');
    echo('</div>');
    return;
}

$question_id = $_GET['question_id'];
try{
    $question = new Question($question_id);
}catch(Exception $e){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie istnieje takie pytanie.');
    echo('</div>');
    return;
}

if(!Session::$current_user->checkAccessForSeason($question->season)){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Brak dostępu</h1>');
    echo('  Nie masz dostępu do tego głosowania z roku '.Season::getSchoolyearForSeason($question->season));
    echo('</div>');
    return;
}

echo(QuestionDrawer::prepareCodeForDetails($question));
?>