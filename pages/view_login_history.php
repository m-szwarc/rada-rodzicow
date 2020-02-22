<?php
if(!Session::$current_user->checkPriv(PRIV_SITE_CONFIG)) return -1;
Log::write('Wyświetlono historię logowań', LOG_ADMINISTRATION);

$logins = LogInService::getLoginHistory();
?>
<div class="card">
    <h1 class="card-header">Historia logowań</h1>
    <div class="overflow-x-container">
        <table>
            <tr>
                <th>Data</th>
                <th>Nazwa użytkownika</th>
                <th>Adres IP</th>
                <th></th>
            </tr>
            <?php
                foreach($logins as $l){
                    echo('<tr>');
                    echo('<td>'.$l->date.'</td>');
                    echo('<td>'.$l->login.'</td>');
                    echo('<td>'.$l->ip.'</td>');
                    echo('<td>'.LogInService::getIconForResult($l->result).'</td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</div>