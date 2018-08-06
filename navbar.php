<div class="nav-header" onclick="toggleNavbar()">
    <span class="nav-toggle">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0h24v24H0z" fill="none"/>
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
    </span>
    <span>Rada Rodziców</span>
</div>
<div id="nav-content" class="nav-content">
    <ul class="navbar-left">
        <li class="user-header">
            <span>
                <a class="close-nav-drawer" href="javascript:toggleNavbar()"><i class="fa fa-times fa-fw"></i></a>
                <?php echo('<b id="user-full-name">'.Session::$current_user->getFullName().'</b>'); ?>
                <a href="logout" class="logout-mobile" title="Wyloguj"><i class="fa fa-sign-out fa-fw"></i></a>
            </span>
        </li>
        <li <?php if($path == 'question_list') echo('class="active"'); ?>><a href="question_list"><i class="fa fa-home fa-fw"></i> Strona główna</a></li>
        <li <?php if($path == 'account') echo('class="active"'); ?>><a href="account"><i class="fa fa-user fa-fw"></i> Twoje konto</a></li>
        <?php
        if(Session::$current_user->checkPriv(PRIV_ADD_QUESTION)) echo('<li '.($path == 'add_question' ? 'class="active"' : '').'><a href="add_question"><i class="fa fa-plus fa-fw"></i> Zadaj pytanie</a></li>');
        if(Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) echo('<li '.($path == 'view_accounts' ? 'class="active"' : '').'><a href="view_accounts"><i class="fa fa-users fa-fw"></i> Konta użytkowników</a></li>');
        if(Session::$current_user->checkPriv(PRIV_SITE_CONFIG)){
            echo('<li class="dropdown"><a href="#" class="dropdown-header"><i class="fa fa-bar-chart fa-fw"></i> Diagnostyka</a><div class="dropdown-content">');
            echo('    <a href="view_phpinfo" '.($path == 'view_phpinfo' ? 'class="active"' : '').'><i class="fa fa-cog fa-fw"></i> Konfiguracja serwera</a>');
            echo('    <a href="view_exceptions" '.($path == 'view_exceptions' ? 'class="active"' : '').'><i class="fa fa-bug fa-fw"></i> Zgłoszone błędy</a>');
            echo('    <a href="view_login_history" '.($path == 'view_login_history' ? 'class="active"' : '').'><i class="fa fa-users fa-fw"></i> Historia logowań</a>');
            echo('</div></li>');
        }
        ?>
        <li <?php if($path == 'help') echo('class="active"'); ?>><a href="help"><i class="fa fa-question fa-fw"></i> Pomoc</a></li>
    </ul>
    <?php
    $appendix = '';
    if(Session::$current_user->appendix != '' && !is_null(Session::$current_user->appendix)) $appendix = ' ('.Session::$current_user->appendix.')';
    ?>
    <ul class="navbar-right">
        <li><span><?php echo('<b id="user-full-name">'.Session::$current_user->getFullName().'</b>'.$appendix); ?></span></li>
        <li><a href="logout"><i class="fa fa-sign-out fa-fw"></i> Wyloguj</a></li>
    </ul>
</div>
<div class="mobile-fill-right-edge" onclick="toggleNavbar()" id="nav-right-edge"></div>