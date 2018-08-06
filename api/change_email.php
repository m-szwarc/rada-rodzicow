<?php
include('../autoloader.php');

$new_email = $_POST['new'];
$session_key = $_POST['session'];

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
$user = Session::$current_user;

if(!isValidEmail($new_email)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Podany adres e-mail nie jest prawidłowy.",
        "details": "Adres e-mail musi mieć postać osoba@domena"
    }
    <?php
    exit;
}

try{
    $user->changeEmail($new_email);
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się zmienić Twojego adresu e-mail.",
        "details": "<?php echo(DB::getConnector()->error) ?>"
    }
    <?php
    exit;
}
?>
{
    "result": "success",
    "code": 200,
    "description": "Twój adres e-mail został zmieniony.",
    "details": ""
}