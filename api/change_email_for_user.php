<?php
include('../autoloader.php');

$uid = $_POST['uid'];
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

if(!$user->checkPriv(PRIV_MANAGE_ACCOUNTS)){
    ?>
    {
        "result": "error",
        "code": 403,
        "description": "Brak dostępu.",
        "details": "Nie masz odpowiednich uprawnień."
    }
    <?php
    exit;
}

$target_user = new User($uid);

if($target_user->checkPriv(PRIV_ADMIN) && !$user->checkPriv(PRIV_ADMIN)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Nie możesz zarządzać kontem administratora bez uprawnień administracyjnych.",
        "details": ""
    }
    <?php
    exit;
}

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
    $target_user->changeEmail($new_email);
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się zmienić adresu tego użytkownika e-mail.",
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