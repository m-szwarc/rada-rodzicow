<?php
include('autoloader.php');

if(isset($_GET['file'])){
  $file = $_GET['file'];
}else{
  header('X-Response-Code: 404', true, 404);
  exit;
}

DB::connect();

try{
  $f = new File($file);
}catch (Exception $e){
  header('X-Response-Code: 404', true, 404);
  DB::close();
  exit;
}

// Należy sprawdzić uprawnienia
if($f->question_id != 0){
  Session::start();
  Session::restoreUser();
  if(!LogInService::isUserAuthorized(Session::$current_user)){
    header('X-Response-Code: 403', true, 403);
    exit;
  }
  $q = new Question($f->question_id);
  if(!Session::$current_user->checkAccessForSeason($q->season)){
    header('X-Response-Code: 403', true, 403);
    exit;
  }
}

if(!file_exists($f->real_path)){
  header('X-Response-Code: 404', true, 404);
  exit;
}

header('Content-Type: '.getMimeTypeByFileName($f->getBaseName()));
if(doUseDownload(getMimeTypeByFileName($f->getBaseName()))) header('Content-Disposition: attachment; filename="'.$f->getBaseName().'"'); 
readfile($f->real_path);
?>