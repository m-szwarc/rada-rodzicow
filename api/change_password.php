<?php
include('../autoloader.php');

$oldpass = $_POST['old'];
$newpass = $_POST['new'];
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

if(!$user->checkPassword(hash('sha256', $oldpass))){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Podano błędne stare hasło.",
        "details": "Stare hasło nie pasuje do zapisanego w bazie danych."
    }
    <?php
    exit;
}

if(!isGoodPass($newpass)){
    ?>
    {
        "result": "error",
        "code": 400,
        "description": "Nowe hasło nie zawiera wszystkich niezbędnych znaków.",
        "details": "Nowe hasło musi zawierać wielkie i małe litery oraz cyfry lub symbole."
    }
    <?php
    exit;
}

try{
    $user->changePassword($newpass);
}catch(Exception $e){
    ?>
    {
        "result": "error",
        "code": 500,
        "description": "Nie udało się zmienić Twojego hasła.",
        "details": "<?php echo(DB::getConnector()->error) ?>"
    }
    <?php
    exit;
}
?>
{
    "result": "success",
    "code": 200,
    "description": "Twoje hasło zostało zmienione.",
    "details": ""
}
<?php
function isGoodPass($pass){
    if(!preg_match('#[a-ząćęłńóśźż]#', $pass)) return false;
    if(!preg_match('#[A-ZĄĆĘŁŃÓŚŹŻ]#', $pass)) return false;
    if(!preg_match('#[0-9`~!@\#$%^&*()\-_=+\[\]{};:<,>.\/?|\\\'"]#', $pass)) return false;
    return strlen($pass) > 7;
}
?>