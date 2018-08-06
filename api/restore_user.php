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

try{
    $target_user->restore();
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się przywrócić tego konta.",
        "details": "<?php echo(DB::getConnector()->error) ?>"
    }
    <?php
    exit;
}
?>
{
    "result": "success",
    "code": 200,
    "description": "Konto zostało przywrócone.",
    "details": ""
}