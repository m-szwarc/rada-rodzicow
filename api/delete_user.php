<?php
include('../autoloader.php');

$uid = $_POST['uid'];
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
        "description": "Nie możesz usunąć konta administratora bez uprawnień administracyjnych.",
        "details": ""
    }
    <?php
    exit;
}

if($uid == $user->id){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Nie możesz usunąć swojego konta.",
        "details": ""
    }
    <?php
    exit;
}

try{
    $target_user->delete();
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się usunąć tego konta.",
        "details": "<?php echo(DB::getConnector()->error) ?>"
    }
    <?php
    exit;
}
?>
{
    "result": "success",
    "code": 200,
    "description": "Konto zostało usunięte.",
    "details": ""
}