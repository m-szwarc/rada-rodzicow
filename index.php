<?php
error_reporting(-1); //Debug: -1
ini_set("display_errors", 1); //Debug: 1

define('USE_HTTPS', false);
if((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "") && USE_HTTPS){
    $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$redirect);
    exit;
}

include('autoloader.php');

DB::connect();
Session::start();

$path = 'question_list';
if(isset($_GET['path'])) $path = $_GET['path'];

if(isset($_POST['log_user_in']) && $_POST['log_user_in'] == 'login'){
    if(isset($_POST['login']) && isset($_POST['pass'])){
        LogInService::tryToLogIn($_POST['login'], $_POST['pass']);
        if($path == 'logout') $path = 'question_list';
    }
}

if(LogInService::$lastLoginResult == 1){
    Session::setCurrentUser(LogInService::$loggedInUser);
    Session::renew();
}else{
    Session::restoreUser();
}

if($path == 'logout'){
    Session::logUserOut();
    $path = 'question_list';
}
if($path == 'login') $path = 'question_list';
if(!file_exists('pages/'.$path.'.php')) $path = 'question_list';
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="theme-color" content="#00bcd4" />
        <style>
            button {background:transparent} /* Aby uniknąć natychmiastowego przejścia po otwarciu strony */
        </style>
        <link rel="stylesheet" href="files/style" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400i,500,700" />
        <link rel="stylesheet" href="https://use.fontawesome.com/8ab01be9cd.css" />
        <script src="files/script"></script>
        <script src="files/xhr" async></script>
        <script src="files/toasts" async></script>
        <link rel="shortcut icon" href="favicon.ico" />
        <title>Rada Rodziców przy ZSS w Poznaniu</title>
    </head>
    <body onload="onBodyLoad()">
        <?php
            if(!LogInService::isUserAuthorized(Session::$current_user)){
                ?>
                <header class="login-header header-long">
                    Rada Rodziców przy Zespole Szkół Salezjańskich w Poznaniu
                </header>
                <header class="login-header header-short">
                    Rada Rodziców przy ZSS w Poznaniu
                </header>

                <?php
                echo('<main class="login-container">');

                if($path == 'forgot') include('pages/forgot.php');
                else include('pages/login.php');

                echo('</main>');
                exit;
            }
            
            echo('<nav>');

            include('navbar.php');

            echo('</nav>');
            echo('<main>');

            ?>
            <noscript>
                <div class="card">
                    <big class="card-header">
                        <i class="fa fa-exclamation-triangle red"></i>
                        W Twojej przeglądarce została wyłączona obsługa JavaScriptu
                    </big>
                    Ta strona używa JavaScriptu do obsługi głosowań. Jeżeli nie włączysz jego obsługi w ustawieniach swojej
                    przeglądarki, główne funkcje tej strony (na przykład oddawanie głosów) nie będą działać.
                </div>
            </noscript>
            <?php

            try{
                $result = include('pages/'.$path.'.php');
                if($result == -1 || $result === false){
                    if($result == -1) Log::write('Zablokowany nieautoryzowany dostęp do strony '.$path.'.');
                    include('pages/question_list.php');
                }
            }catch(Exception $e){
                echo('<div class="card">');
                echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił nieznany błąd</h1>');
                echo($e->getMessage());
                echo('  <div class="card-buttons">');
                echo('    <a class="button flat colored right" href="question_list">Przejdź do strony głównej</a>');
                echo('  </div>');
                echo('</div>');
            }

            echo('</main>');
            echo('<div class="toast-container" id="toast-container"></div>');
            echo('<div class="copyright-note">Copyright &copy; 2018, Marcin Szwarc</div>');
        ?>
    </body>
</html>
<?php
DB::close();
?>