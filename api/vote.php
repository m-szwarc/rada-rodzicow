<?php
include('../autoloader.php');

$answer_id = $_POST['aid'];
$user_id = $_POST['uid'];
$question_id = $_POST['qid'];
$session_key = $_POST['session'];

if(!is_int_number($answer_id)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Odpowiedź została przesłana w błędny sposób.",
        "details": "<?php echo($answer_id); ?> nie jest poprawnym identyfikatorem odpowiedzi."
    }
    <?php
    exit;
}

if(!is_int_number($user_id)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Użytkownik został przesłany w błędny sposób.",
        "details": "<?php echo($user_id); ?> nie jest poprawnym identyfikatorem użytkownika."
    }
    <?php
    exit;
}

if(!is_int_number($question_id)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Pytanie zostało przesłane w błędny sposób.",
        "details": "<?php echo($question_id); ?> nie jest poprawnym identyfikatorem pytania."
    }
    <?php
    exit;
}

DB::connect();

try{
    Session::start($session_key);
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Nie można utworzyć sesji.",
        "details": "Podany klucz sesji (<?php echo($session_key); ?>) jest nieprawidłowy."
    }
    <?php
    exit;
}

Session::restoreUser();
$session_uid = Session::$current_user->id;

if($session_uid != $user_id){
    ?>
    {
        "result": "error",
        "code": 403,
        "description": "Nie możesz głosować za innego użytkownika.",
        "details": "$session_uid != $user_id"
    }
    <?php
    exit;
}

$question = new Question($question_id);
if($question->season != Season::getCurrent()){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Kadencja, w której to pytanie zostało zadane, upłynęła",
        "details": ""
    }
    <?php
    exit;
}


if($question->checkUserHasVoted(Session::$current_user)){
    ?>
    {
        "result": "error",
        "code": 409,
        "description": "Oddałeś już głos na to pytanie.",
        "details": ""
    }
    <?php
    exit;
}

if(!Answer::answerExistsForQuestion($answer_id, $question_id)){
    ?>
    {
        "result": "error",
        "code": 405,
        "description": "Przesyłana odpowiedź nie należy do tego pytania.",
        "details": "Odpowiedź <?php echo($naswer_id) ?> nie jest poprawną odpowiedzią na pytanie <?php echo($question_id); ?>"
    }
    <?php
    exit;
}

if(!$question->vote($user_id, $answer_id)){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się zapisać Twojego głosu do bazy danych",
        "details": "<?php echo(DB::getError()) ?>"
    }
    <?php
    exit;
}

?>
{
    "result": "success",
    "code": 200,
    "description": "Twój głos został zapisany.",
    "details": ""
}