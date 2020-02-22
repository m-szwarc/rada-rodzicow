<?php
if(!Session::$current_user->checkPriv(PRIV_SITE_CONFIG)) return -1;
Log::write('Wyświetlono listę błędów', LOG_ADMINISTRATION);

$exceptions = RRException::getAll();
?>
<div class="card wide-card">
    <h1 class="card-header">Zgłoszone błędy</h1>
    <div class="overflow-x-container">
        <table class="full-width">
            <tr>
                <th>Data</th>
                <th class="exception-file-column">Plik</th>
                <th class="exception-file-column">Linia</th>
                <th>Treść błędu</th>
                <th></th>
            </tr>
            <?php
                foreach($exceptions as $key => $e){
                    echo('<tr>');
                    echo('<td>'.$e->date.'</td>');
                    echo('<td class="exception-file-column">'.$e->file.'</td>');
                    echo('<td class="exception-file-column">'.$e->line.'</td>');
                    echo('<td>'.(strlen($e->message) > 50 ? substr($e->message, 0, 50).'<span class="secondary">...</span>' : $e->message).'</td>');
                    echo('<td><button type="button" class="flat small" onclick="toggleVisibility(\'exception-row'.$key.'\')"><i class="fa fa-ellipsis-v"></i></button></td>');
                    echo('</tr>');

                    echo('<tr class="hidden" id="exception-row'.$key.'">');
                    echo('<td colspan="5">');
                    echo('<b>REQUEST_URI:</b> '.$e->request_uri.'<br />');
                    echo('<b>POST:</b> '.$e->post_data.'<br />');
                    echo('<b>Plik:</b> '.$e->file.':'.$e->line.'<br />');
                    echo('<b>Treść:</b> '.$e->message.'<br />');
                    echo('</td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</div>