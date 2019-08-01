<?php
$first_name = Session::$current_user->first_name;
$last_name = Session::$current_user->last_name;
$email = Session::$current_user->email;
?>
<div class="card">
    <h1 class="card-header">Twoje konto</h1>

    <section>
        <h2>Twoje dane</h2>
        <span class="secondary">Imię: </span><?php echo($first_name); ?><br />
        <span class="secondary">Nazwisko: </span><?php echo($last_name); ?><br />
        <span class="secondary">Adres e-mail: </span><span id="user-email"><?php echo(empty($email) ? '<i>nieustawiony</i>' : $email); ?></span><br />
    </section>

    <section>
        <h2>Zmiana hasła</h2>
        <div class="change-password-container">
            <label class="oldpass-label" for="oldpass">Stare hasło:</label>
            <label class="newpass-label" for="newpass">Nowe hasło:</label>
            <label class="pwtpass-label" for="pwtpass">Powtórz hasło:</label>
            
            <input type="password" id="oldpass" class="oldpass-input" />
            <input type="password" id="newpass" class="newpass-input" />
            <input type="password" id="pwtpass" class="pwtpass-input" />
        </div>
        <i class="secondary">
            Hasło musi zawierać małe i wielkie litery oraz cyfry albo inne znaki, takie jak: myślnik, dwukropek, wykrzyknik itp.<br />
            Jego długość musi wynosić minimum 8 znaków.
        </i><br />
        <button class="flat colored" type="button" onclick="changePassword('oldpass', 'newpass', 'pwtpass');">Zmień hasło</button>
    </section>

    <section>
        <h2>Zmiana adresu e-mail</h2>
        Jeżeli chcesz zmienić adres e-mail, podaj go tutaj:<br />
        <input type="email" id="new-email" />
        <button class="flat colored" type="button" onclick="changeEmail('new-email', 'user-email')">Zapisz</button><br />
        <i class="secondary">
            Podany tu adres e-mail zostanie wykorzystany tylko i wyłącznie do komunikacji w ramach Rady Rodziców.
        </i>
    </section>

    <?php
        if(Session::$current_user->checkPriv(PRIV_ADMIN)){
            $user = Session::$current_user;
            ?>
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
            <?php
        }
    ?>
</div>