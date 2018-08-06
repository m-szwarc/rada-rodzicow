<?php
include('constants.php');
include('functions.php');
include('mime.php');

function class_loader($name){
    $name = strtolower($name);
    if(file_exists($name.'.class.php')) include($name.'.class.php');
    if(file_exists('../'.$name.'.class.php')) include('../'.$name.'.class.php');
}

spl_autoload_register('class_loader');
?>