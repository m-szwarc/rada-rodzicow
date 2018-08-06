<?php
if(isset($_POST['forgot_password']) && $_POST['forgot_password'] == 'forgot'){
    if(isset($_POST['login']) && isset($_POST['email'])){
        PasswordResetService::tryToResetPassword($_POST['login'], $_POST['email']);
    }
}
?>
<div class="card">
    <h1 class="center card-header">Resetowanie hasła</h1>
    <form action="forgot" method="POST" class="login-form">
        <div class="card-login">
            <i class="fa fa-unlock-alt login-icon"></i>
            <label for="login_field" class="login-label">Login:</label>
            <input type="text" id="login_field" class="login-input" name="login" maxlength="32"/>
            <label for="email_field" class="password-label">E-mail:</label>
            <input type="email" id="email_field" class="password-input" name="email"/>

            <input type="hidden" name="forgot_password" value="forgot" />
            <p class="secondary justify forgot-password-notice">
                Jeżeli login oraz adres e-mail należą do tego samego konta,
                otrzymasz wiadomość e-mail z nowym hasłem.
            </p>
        </div>
        <div class="card-buttons only-inline">
            <button type="submit" class="flat colored login-button">Dalej</button>
            <a class="button flat right" href="login">Wróć do logowania</a>
        </div>
    </form>
</div>
<?php
if(PasswordResetService::$lastState >= 0) echo('<div class="center">Jeżeli adres e-mail i login należą do tego samego konta, zostanie wysłana wiadomość e-mail.</div>');
if(PasswordResetService::$lastState == -2) echo('<div class="wrong-login-data">Nie udało się wysłać wiadomości e-mail. W celu zmiany hasła skontaktuj się z administratorem.</div>');
?>