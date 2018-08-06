<?php
if(!Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) return -1;

$users = User::getDeleted();
?>
<div class="card">
    <h1 class="card-header">Usunięci użytkownicy</h1>
    <div class="overflow-y-container">
        <table>
            <tr>
                <th>Imię i nazwisko</th>
                <th class="login-column">Login</th>
                <th></th>
            </tr>
            <?php
            foreach($users as $user){
                if($user->checkPriv(PRIV_ADMIN)) if(!Session::$current_user->checkPriv(PRIV_ADMIN)) continue;

                echo('<tr id="user-row-'.$user->id.'">');
                echo('  <td>'.$user->getFullName().'</td>');

                echo('  <td class="login-column">'.$user->login.'</td>');
                echo('  <td><button class="flat" onclick="restoreUser('.$user->id.')"><i class="fa fa-reply"></i><span class="hide-on-small"> Przywróć konto</span></button></td>');
                echo('</tr>');
            }
            if(count($users) == 0){
                echo('<tr><td colspan="3" class="center"><i class="secondary">Brak usuniętych użytkowników</i></td></tr>');
            }
            ?>
        </table>
    </div>
</div>