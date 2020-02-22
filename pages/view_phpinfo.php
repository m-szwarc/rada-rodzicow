<?php
if(!Session::$current_user->checkPriv(PRIV_SITE_CONFIG)) return -1;
Log::write('Wyświetlono stronę phpinfo', LOG_ADMINISTRATION);

echo('<div class="phpinfo">');

ob_start();
phpinfo();
$pinfo = ob_get_contents();
ob_end_clean();
 
$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
echo($pinfo);

echo('</div>');
?>