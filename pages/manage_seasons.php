<?php
if(!Session::$current_user->checkPriv(PRIV_MANAGE_ACCOUNTS)) return -1;
Log::write('Wyświetlono stronę zarządzania kadencjami', LOG_MANAGE_USERS);

$users = User::getAll();
?>
<div class="card">
    <h1 class="card-header">Zarządaj kadencjami użytkowników</h1>
    <div class="overflow-x-container">
        <table>
            <tr>
                <th class="sticky-column">Użytkownik</th>
                <?php
                    for($j = Season::getCurrent() + 1; $j <= Season::getCurrent() + 32; $j++){
                        $i = $j % 32;
                        if(Season::isFutureSeason($i)) continue;
                        if(Season::getYearForSeason($i) <= 2014) continue;
                        echo('<th>'.Season::getSchoolyearForSeason($i).'</th>');
                    }
                ?>
                <th></th>
            </tr>
            <?php
                foreach($users as $user){
                    $disabled = ($user->checkPriv(PRIV_ADMIN) && !Session::$current_user->checkPriv(PRIV_ADMIN));
                    if($disabled) continue;
                    echo('<tr>');
                    echo('<td class="sticky-column">'.$user->getFullName());
                    echo($user->checkPriv(PRIV_ADMIN) ? ' <i class="fa fa-shield cyan admin-shine" title="Administrator"></i>' : '');
                    echo('<input type="hidden" id="season-data'.$user->id.'" value="'.$user->season.'" /></td>');
                    for($j = Season::getCurrent() + 1; $j <= Season::getCurrent() + 32; $j++){
                        $i = $j % 32;
                        if(Season::isFutureSeason($i)) continue;
                        if(Season::getYearForSeason($i) <= 2014) continue;
                        echo('<td class="center"><input type="checkbox" name="season[]"'.($disabled ? ' disabled' : '').' onchange="updateSeasonData('.$user->id.', '.$i.')" value="'.$i.'" id="cb'.$i.'-'.$user->id.'" '.($user->checkAccessForSeason($i) ? 'checked ' : '').'/><label for="cb'.$i.'-'.$user->id.'"></label></td>');
                    }
                    echo('<td><button type="button" class="flat" onclick="saveSeasonData('.$user->id.')"'.($disabled ? ' disabled' : '').'>Zapisz</button></td>');
                    echo('</tr>');
                }
            ?>
        </table>
    </div>
</div>