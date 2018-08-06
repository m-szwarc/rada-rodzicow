<?php
if(!Session::$current_user->checkPriv(PRIV_ADD_QUESTION)) return -1;

if(!isset($_GET['question_id']) && !isset($_POST['question_id'])){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie został podany numer pytania.');
    echo('</div>');
    return;
}

$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : $_GET['question_id'];
try{
    $question = new Question($question_id);
}catch(Exception $e){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie istnieje takie pytanie.');
    echo('</div>');
    return;
}

if($question->author->id != Session::$current_user->id){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Możesz usuwać tylko pytania utworzone przez siebie.');
    echo('</div>');
    return;
}

try{
    Question::delete($question->id);

    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-check-circle green"></i> Usunięto pytanie</h1>');
    echo('  Pytanie zostało pomyślnie usunięte.');
    echo('  <div class="card-buttons">');
    echo('    <a class="button flat right" href="question_list">Wróć do strony głównej</a>');
    echo('  </div>');
    echo('</div>');
}catch(Exception $e){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo(   $e->getMessage());
    echo('  <pre>'.$e.'</pre>');
    echo('</div>');
}
?>