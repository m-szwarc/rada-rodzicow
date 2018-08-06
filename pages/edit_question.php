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
    echo('  Możesz edytować tylko pytania utworzone przez siebie.');
    echo('</div>');
    return;
}

$answers = $question->answers;
$use_answer = array();
if(isset($_POST['text'])){
    $answers = array();
    foreach($_POST['value_answer'] as $aid => $text){
        $answers[] = new Answer($aid, is_numeric($aid) ? $question->id : 0, $text);
        $use_answer[$aid] = isset($_POST['use_answer'][$aid]);
    }
}else{
    foreach($answers as $answer){
        $use_answer[$answer->id] = true;
    }
}

if(isset($_POST['text'])){
    $text = $_POST['text'];
    $time_limit = 0;

    if(isset($_POST['use_limit'])){
        $time_limit = strtotime($_POST['limit1'].' '.$_POST['limit2']);
    }

    try{
        $question->change($text, $time_limit);

        foreach($answers as $answer){
            if($answer->question == 0){
                if($use_answer[$answer->id]) Answer::create($question->id, $answer->text);
            }else{
                if($use_answer[$answer->id]) Answer::setTextForAnswer($answer->id, $answer->text);
                else Answer::deleteAnswer($answer->id);
            }
        }
        
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-check-circle green"></i> Zmodyfikowano pytanie</h1>');
        echo('  Pytanie zostało zmodyfikowane.<br />Link bezpośredni: ');
        echo('  <code>https://szkola-poznan.salezjanie.pl/rada/view_question?question_id='.$question->id.'</code>');
        echo('  <div class="card-buttons">');
        echo('    <a class="button flat colored right" href="view_question?question_id='.$question->id.'">Przejdź do pytania</a>');
        echo('    <a class="button flat right" href="question_list">Wróć do strony głównej</a>');
        echo('  </div>');
        echo('</div>');

        exit;
    }catch(Exception $e){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo(   $e->getMessage());
        echo('  <pre>'.$e.'</pre>');
        echo('</div>');
    }
}

$text = $question->text;

if($question->time_limit > 0){
    $date_limit = date('Y-m-d', $question->time_limit);
    $time_limit = date('H:i', $question->time_limit);
}else{
    $date_limit = date('Y-m-d');
    $time_limit = date('H:i');
}
$use_limit = $question->time_limit > 0;

if(isset($_POST['text'])) $text = $_POST['text'];
if(isset($_POST['limit1'])) $date_limit = $_POST['limit1'];
if(isset($_POST['limit2'])) $time_limit = $_POST['limit2'];
if(isset($_POST['text'])) $use_limit = isset($_POST['use_limit']);

$next_aid = 0;
if(isset($_POST['next_aid'])) $next_aid = $_POST['next_aid'];
?>
<div class="card">
    <h1 class="card-header">Edytuj pytanie</h1>
    <form action="edit_question" method="POST">
        <input type="hidden" name="question_id" value="<?php echo($question_id); ?>" />
        <input type="hidden" name="next_aid" value="<?php echo($next_aid); ?>" id="next-aid" />

        <section>
            <h2>Treść pytania</h2>
            <textarea name="text" rows="5"><?php echo($text); ?></textarea>
        </section>

        <section>
            <h2>Odpowiedzi</h2>
            <div id="answer-container">
                <?php
                foreach($answers as $answer){
                    echo('<div class="answer-box">');
                    echo('  <input type="checkbox" name="use_answer['.$answer->id.']" value="'.$answer->id.'" id="use-answer'.$answer->id.'" '.($use_answer[$answer->id] ? 'checked' : '').' />');
                    echo('  <label for="use-answer'.$answer->id.'"></label>');
                    echo('  <input type="text" name="value_answer['.$answer->id.']" value="'.$answer->text.'" />');
                    echo('</div>');
                }
                ?>
            </div>
            <button class="flat" type="button" onclick="addAnswer()"><i class="fa fa-plus"></i> Dodaj</button>
        </section>

        <section>
            <h2>Limit czasu na wypełnienie</h2>
            <input type="checkbox" name="use_limit" id="use-limit" onchange="useLimitChanged(this)" <?php if($use_limit) echo('checked'); ?>/>
            <label for="use-limit">Użyj limitu czasu</label><br />
            <input type="date" name="limit1" id="limit1" class="group-begin" value="<?php echo($date_limit); ?>" <?php if(!$use_limit) echo('disabled'); ?>/><!--
         --><input type="time" name="limit2" id="limit2" class="group-end" value="<?php echo($time_limit); ?>" <?php if(!$use_limit) echo('disabled'); ?>/>
            <span style="display:none" id="limit-info" class="secondary">
                <br />RRRR-MM-DD | GG:MM
            </span>
        </section>

        <div class="card-buttons">
            <button class="flat colored right" type="submit">Zapisz</button>
        </div>
    </form>
</div>