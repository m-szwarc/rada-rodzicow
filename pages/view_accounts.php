<?php
if(!Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) return -1;

$users = User::getAll();
?>
<div class="card">
    <h1 class="card-header">Konta użytkowników</h1>
    <div class="overflow-y-container">
        <table>
            <tr>
                <th class="sticky-row">Imię i nazwisko</th>
                <th class="sticky-row">Adres e-mail</th>
                <th class="sticky-row send-mail-column"></th>
                <th class="sticky-row login-column">Login</th>
                <th class="sticky-row sticky-right-column"></th>
            </tr>
            <?php
            foreach($users as $user){
                if($user->checkPriv(PRIV_ADMIN) && !Session::$current_user->checkPriv(PRIV_ADMIN)) continue;

                echo('<tr>');
                echo('  <td>'.$user->getFullName().($user->checkPriv(PRIV_ADMIN) ? ' <i class="fa fa-shield cyan admin-shine" title="Administrator"></i>' : '').'</td>');

                if(!empty($user->email)) echo('  <td>'.$user->email.'</td><td class="send-mail-column"><a href="mailto:'.$user->email.'" title="Wyślij wiadomość e-mail"><i class="fa fa-envelope-o"></i></a></td>');
                else echo('  <td><i class="secondary">brak</i></td><td class="send-mail-column"></td>');

                echo('  <td class="login-column">'.$user->login.'</td>');
                echo('  <td class="sticky-right-column"><a class="button flat" href="manage_account?user_id='.$user->id.'"><i class="fa fa-cog"></i><span class="hide-on-small"> Zarządzaj</span></a></td>');
                echo('</tr>');
            }
            ?>
        </table>
    </div>

    <div class="card-buttons">
        <a href="new_user" class="button flat colored"><i class="fa fa-user-plus"></i> Nowy użytkownik</a>
        <a href="deleted_users" class="button flat"><i class="fa fa-user-o"></i> Wyświetl usunięte konta</a>
        <a href="manage_seasons" class="button flat"><i class="fa fa-calendar-o"></i> Kadencje Rady Rodziców</a>
    </div>
</div>