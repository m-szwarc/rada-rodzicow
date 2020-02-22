<?php
if(!Session::$current_user->checkPriv(PRIV_ADD_QUESTION)) return -1;

if(isset($_POST['text'])){
    $text = $_POST['text'];
    $answers = explode("\n", $_POST['answers']);
    $time_limit = 0;

    if(isset($_POST['use_limit'])){
        $time_limit = strtotime($_POST['limit1'].' '.$_POST['limit2']);
    }

    $question_id = -1;

    try{
        $question_id = Question::create($text, $answers, $time_limit, Session::$current_user);
        
        $files = reArrayFiles($_FILES['files']);
        File::upload($files, $question_id);
        Log::write('Dodano pytanie');

        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-check-circle green"></i> Dodano pytanie</h1>');
        echo('  Pytanie zostało dodane.<br />Link bezpośredni: ');
        echo('  <code>https://szkola-poznan.salezjanie.pl/rada/view_question?question_id='.$question_id.'</code>');
        echo('  <div class="card-buttons">');
        echo('    <a class="button flat colored right" href="view_question?question_id='.$question_id.'">Przejdź do pytania</a>');
        echo('    <a class="button flat right" href="question_list">Wróć do strony głównej</a>');
        echo('  </div>');
        echo('</div>');

        exit;
    }catch(Exception $e){
        if($question_id != -1) Question::delete($question_id);
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo($e->getMessage());
        echo('</div>');
    }
}

$text = '';
$answers = 'Tak&#10;Nie&#10;Wstrzymuję się';
$date_limit = date('Y-m-d');
$time_limit = '20:00';
if(isset($_POST['text'])) $text = $_POST['text'];
if(isset($_POST['answers'])) $answers = $_POST['answers'];
if(isset($_POST['limit1'])) $date_limit = $_POST['limit1'];
if(isset($_POST['limit2'])) $time_limit = $_POST['limit2'];
?>
<div class="card">
    <h1 class="card-header">Zadaj pytanie</h1>
    <form action="add_question" method="POST" enctype="multipart/form-data">
        <section>
            <h2>Treść pytania</h2>
            <textarea name="text" rows="5"><?php echo($text); ?></textarea>
            <i class="secondary right">Możesz używać znaczników HTML</i>
        </section>

        <section>
            <h2>Odpowiedzi</h2>
            <textarea name="answers" rows="4"><?php echo($answers); ?></textarea>
            <i class="secondary right">Każda w osobnej linii</i>
        </section>

        <section>
            <h2>Limit czasu na wypełnienie</h2>
            <input type="checkbox" name="use_limit" id="use-limit" checked/>
            <label for="use-limit">Użyj limitu czasu</label><br />
            <input type="date" name="limit1" id="limit1" class="group-begin" value="<?php echo($date_limit); ?>"/><!--
         --><input type="time" name="limit2" id="limit2" class="group-end" value="<?php echo($time_limit); ?>"/>
            <span style="display:none" id="limit-info" class="secondary">
                <br />RRRR-MM-DD | GG:MM
            </span>
        </section>

        <section>
            <h2>Dołącz pliki</h2>
            <input type="file" class="inputfile" name="files[]" id="file-input" data-multiple-caption="Wybrano: {count}" multiple />
            <label for="file-input" class="flat">
                <i class="fa fa-upload"></i>
                Wybierz pliki (<span class="secondary">Nie wybrano</span>)
            </label>
        </section>

        <div class="card-buttons">
            <button class="flat colored right" type="submit">Zapisz</button>
        </div>
    </form>
</div>

<script>
    checkInputDateSupport("limit1", "limit2", "limit-info");
</script>