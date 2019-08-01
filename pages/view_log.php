<?php
if(!Session::$current_user->checkPriv(PRIV_SITE_CONFIG)) return -1;

$log = Log::read();
?>
<div class="card">
    <h1 class="card-header">Log strony</h1>
    <div class="overflow-x-container">
        <table>
            <tr>
                <th></th>
                <th>Data</th>
                <th>Adres IP</th>
                <th>Treść</th>
                <th>Hash klucza sesji</th>
            </tr>
            <?php
                foreach($log as $l){
                    echo('<tr>');
                    echo('<td>'.Log::getSourceIcon($l->source).'</td>');
                    echo('<td>'.$l->date.'</td>');
                    echo('<td>'.$l->ip.'</td>');
                    echo('<td>'.$l->message.'</td>');
                    echo('<td title="'.$l->sessionkey_hash.'">'.substr($l->sessionkey_hash, 0, 6).'...'.substr($l->sessionkey_hash, -6).'</td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</div>