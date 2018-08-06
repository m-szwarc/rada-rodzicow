<?php
if(!Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) return -1;

if(!isset($_GET['user_id'])){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie został podany numer użytkownika.');
    echo('</div>');
    return;
}

try{
    $user = new User($_GET['user_id']);
}catch(Exception $e){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie istnieje taki użytkownik.');
    echo('</div>');
    return;
}
$first_name = $user->first_name;
$last_name = $user->last_name;
$email = $user->email;

if($user->checkPriv(PRIV_ADMIN) && !Session::$current_user->checkPriv(PRIV_ADMIN)){
    ?>
        <div class="card">
            <h1 class="card-header">
                <span class="secondary">Konto:</span> <span id="user-name"><?php echo($user->getFullName()); ?></span>
                <i class="fa fa-shield cyan admin-shine" title="Administrator"></i>
            </h1>

            Ten użytkownik jest administratorem. Aby zarządzać tym kontem musisz posiadać uprawnienia adminisracyjne.
        </div>
    <?php
    return;
}
?>
<div class="card">
    <h1 class="card-header">
        <span class="secondary">Konto:</span> <span id="user-name"><?php echo($user->getFullName()); ?></span>
        <?php echo(($user->checkPriv(PRIV_ADMIN) ? ' <i class="fa fa-shield cyan admin-shine" title="Administrator"></i>' : '')); ?>
        <button class="flat right" type="button" title="Zmień imię i nazwisko" onclick="changeUserName(<?php echo($user->id.", '".$user->first_name."', '".$user->last_name."'")?>, 'user-name')"><i class="fa fa-pencil-square-o"></i></button>
    </h1>

    <section>
        <h2>Zmień hasło użytkownikowi</h2>
        <div class="change-password-container">
            <label class="newpass-label" for="newpass">Nowe hasło:</label>
            <label class="pwtpass-label" for="pwtpass">Powtórz hasło:</label>
            <input type="password" id="newpass" class="newpass-input" />
            <input type="password" id="pwtpass" class="pwtpass-input" />
        </div>
        <i class="secondary">
            Hasło musi zawierać małe i wielkie litery oraz cyfry albo inne znaki, takie jak: myślnik, dwukropek, wykrzyknik itp.<br />
            Jego długość musi wynosić minimum 8 znaków.
        </i><br />
        <button class="flat colored" type="button" onclick="changePasswordForUser(<?php echo($user->id) ?>, 'newpass', 'pwtpass');">Zmień hasło</button>
    </section>

    <section>
        <h2>Zmień adres e-mail użytkownikowi</h2>
        <span class="secondary">Aktualny adres e-mail: </span><span id="user-email"><?php echo(empty($email) ? '<i>nieustawiony</i>' : $email); ?></span><br />
        <input type="email" id="new-email" />
        <button class="flat colored" type="button" onclick="changeEmailForUser(<?php echo($user->id) ?>, 'new-email', 'user-email')">Zapisz</button><br />
    </section>

    <section>
        <h2>Kadencje</h2>
        <span class="secondary">Bieżąca kadencja:</span>
        1.10.<?php echo(Season::getYearForCurrentSeason()); ?> - 30.09.<?php echo(Season::getYearForCurrentSeason() + 1); ?><br />
        
        <div class="overflow-x-container">
            <table>
                <tr>
                    <?php
                        for($j = Season::getCurrent() + 1; $j <= Season::getCurrent() + 32; $j++){
                            $i = $j % 32;
                            if(Season::isFutureSeason($i)) continue;
                            if(Season::getYearForSeason($i) <= 2014) continue;
                            echo('<th>'.Season::getSchoolyearForSeason($i).'</th>');
                        }
                    ?>
                </tr>
                <tr>
                    <?php
                        for($j = Season::getCurrent() + 1; $j <= Season::getCurrent() + 32; $j++){
                            $i = $j % 32;
                            if(Season::isFutureSeason($i)) continue;
                            if(Season::getYearForSeason($i) <= 2014) continue;
                            echo('<td class="center"><input type="checkbox" onchange="updateSeasonData('.$user->id.', '.$i.')" value="'.$i.'" id="cb'.$i.'" '.($user->checkAccessForSeason($i) ? 'checked ' : '').'/><label for="cb'.$i.'"></label></td>');
                        }
                    ?>
                </tr>
            </table>
        </div>
        <input type="hidden" id="season-data<?php echo($user->id); ?>" value="<?php echo($user->season); ?>" />
        
        <button class="flat colored" type="button" onclick="saveSeasonData(<?php echo($user->id); ?>)">Zapisz</button>
    </section>

    <section>
        <h2>Uprawnienia</h2>
        <div class="overflow-y-container">
            <table>
                <tr>
                    <th>Rodzaj uprawnień</th>
                    <th></th>
                </tr>
                <?php
                    for($i=0; $i<32; $i++){
                        if(empty($priv_name[$i])) continue;
                        if(!Session::$current_user->checkPriv((1<<$i) | PRIV_ADMIN)) continue;

                        echo('<tr>');
                        echo('<td>'.$priv_name[$i].'</td>');
                        echo('<td><input type="checkbox" id="priv'.$i.'" '.($user->checkPriv(1<<$i) ? 'checked' : '').' onchange="updatePrivData('.$i.')" /><label for="priv'.$i.'"></label></td>');
                        echo('</tr>');
                    }
                ?>
            </table>
            <input type="hidden" id="priv-data" value="<?php echo($user->priv); ?>" />
            <button type="button" class="flat colored" onclick="savePrivData(<?php echo($user->id); ?>)">Zapisz</button>
        </div>
    </section>
</div>

<div class="card button-card">
    <button class="flat red" type="button" onclick="deleteUser(<?php echo($user->id); ?>)">Usuń to konto</button>
</div>