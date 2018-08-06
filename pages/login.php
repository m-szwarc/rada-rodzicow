<div class="card">
    <h1 class="center card-header">Zaloguj się</h1>
    <?php
        $form_action = '';
        if(isset($_GET['path']) && $_GET['path'] == 'logout') $form_action = 'question_list';
    ?>
    <form action="<?php echo($form_action); ?>" method="POST" class="login-form">
        <div class="card-login">
            <i class="fa fa-unlock-alt login-icon"></i>
            <label for="login_field" class="login-label">Login:</label>
            <input type="text" id="login_field" class="login-input" name="login" maxlength="32"/>
            <label for="pass_field" class="password-label">Hasło:</label>
            <input type="password" id="pass_field" class="password-input" name="pass" maxlength="32"/>

            <input type="hidden" name="log_user_in" value="login" />
            <div class="forgot-password-container">
                <a class="no-underline" href="forgot">Zapomniałem hasła</a>
            </div>
        </div>
        <div class="card-buttons only-inline">
            <button type="submit" class="flat colored login-button">Zaloguj się</button>
        </div>
    </form>
</div>
<?php
if(LogInService::$lastLoginResult == 0) echo('<div class="wrong-login-data">Błędny login lub hasło.</div>');
if(LogInService::$lastLoginResult == 2) echo('<div class="wrong-login-data">Twoje konto zostało zdezaktywowane.<br />Skontaktuj się z administratorem, aby uzyskać więcej informacji.</div>');
?>