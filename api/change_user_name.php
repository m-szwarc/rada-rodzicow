<?php
include('../autoloader.php');

$uid = $_POST['uid'];
$fname = $_POST['first_name'];
$lname = $_POST['last_name'];
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

if(empty($fname) || empty($lname)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Imię ani nazwisko nie mogą być puste.",
        "details": "Przesłane imię lub nazwisko jest puste."
    }
    <?php
    exit;
}

try{
    $target_user->changeName($fname, $lname);
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się zmienić imienia i nazwiska tego użytkownika.",
        "details": "<?php echo(DB::getConnector()->error) ?>"
    }
    <?php
    exit;
}
?>
{
    "result": "success",
    "code": 200,
    "description": "Imię i nazwisko tego użytkownika zostały zmienione.",
    "details": ""
}