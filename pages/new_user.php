<?php
if(!Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) return -1;
Log::write('Wyświetlono formularz tworzenia użytkownika', LOG_MANAGE_USERS);

$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$login = isset($_POST['login']) ? $_POST['login'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

if(!empty($first_name) && !empty($last_name) && !empty($login) && !empty($pass) && !empty($pass2)){
    if($pass != $pass2){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo('  Podane hasła różnią się.');
        echo('</div>');
        goto show_form;
    }

    if(!isValidEmail($email) && !empty($email)){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo('  Podany adres e-mail ma zły format.');
        echo('</div>');
        goto show_form;
    }

    if(User::loginExists($login)){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo('  Użytkownik o podanym loginie (<code>'.$login.'</code>) już istnieje.');
        echo('</div>');
        goto show_form;
    }

    try{
        User::create($first_name, $last_name, $login, $pass, !empty($email) ? $email : null);
        Log::write('Utworzono użytkownika', LOG_MANAGE_USERS);
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-check-circle green"></i> Dodano użytkownika</h1>');
        echo('  Użytkownik został dodany pomyślnie.');
        echo('  <div class="card-buttons">');
        echo('    <a class="button flat colored right" href="new_user">Dodaj kolejnego</a>');
        echo('    <a class="button flat right" href="question_list">Wróć do strony głównej</a>');
        echo('  </div>');
        echo('</div>');
    }catch(Exception $e){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo($e->getMessage());
        echo('  <pre>'.$e.'</pre>');
        echo('</div>');
        goto show_form;
    }
    return;
}
show_form:
?>
<div class="card">
    <h1 class="card-header">Dodaj użytkownika</h1>
    <form action="new_user" method="POST">
        <div class="new-user-container">
            <label class="first-name-label" for="first_name">Imię:</label>
            <label class="last-name-label" for="last_name">Nazwisko:</label>
            <label class="newlogin-label" for="login">Login:</label>
            <label class="newpass-label" for="pass">Hasło:</label>
            <label class="pwtpass-label" for="pass2">Powtórz hasło:</label>
            <label class="newemail-label" for="email">Adres e-mail:</label>

            <input class="first-name-input" type="text" id="first_name" name="first_name" value="<?php echo($first_name); ?>" required />
            <input class="last-name-input" type="text" id="last_name" name="last_name" value="<?php echo($last_name); ?>" required />
            <input class="newlogin-input" type="text" id="login" name="login" value="<?php echo($login); ?>" required />
            <input class="newpass-input" type="password" id="pass" name="pass" value="<?php echo($pass); ?>" required />
            <input class="pwtpass-input" type="password" id="pass2" name="pass2" value="<?php echo($pass2); ?>" required />
            <input class="newemail-input" type="email" id="email" name="email" value="<?php echo($email); ?>" />
        </div>
        <div class="card-buttons">
            <button class="flat colored right" type="submit">Zapisz</button>
        </div>
    </form>
</div>