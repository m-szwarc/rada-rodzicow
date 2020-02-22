<?php
if(!Session::$current_user->checkPriv(PRIV_SITE_CONFIG)) return -1;
Log::write('Wyświetlono log strony', LOG_ADMINISTRATION);

$sk_hash = '';
if(isset($_GET['sk_hash'])){
    $sk_hash = strtolower($_GET['sk_hash']);
    if(strlen($sk_hash) != 64) $sk_hash = '';
    if(!ctype_xdigit($sk_hash)) $sk_hash = '';
}

$log = Log::read($sk_hash);
?>
<div class="card wide-card">
    <h1 class="card-header">Log strony</h1>
    <div class="overflow-x-container">
        <table>
            <tr>
                <th></th>
                <th>Data</th>
                <th>Adres IP</th>
                <th>Treść</th>
                <th>Hash klucza sesji</th>
                <th><a title="Usuń filtrowanie" href="?"><i class="fa fa-globe"></i></a></th>
            </tr>
            <?php
                foreach($log as $l){
                    echo('<tr>');
                    echo('<td>'.Log::getSourceIcon($l->source).'</td>');
                    echo('<td>'.$l->date.'</td>');
                    echo('<td>'.$l->ip.'</td>');
                    echo('<td>'.$l->message.'</td>');
                    echo('<td title="'.$l->sessionkey_hash.'">'.substr($l->sessionkey_hash, 0, 6).'...'.substr($l->sessionkey_hash, -6).'</td>');
                    echo('<td><a href="?sk_hash='.$l->sessionkey_hash.'" title="Pokaż tylko tę sesję"><i class="fa fa-filter"></i></a></td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</div>