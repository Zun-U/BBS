<?php
require_once(__DIR__ .'/../config/config.php');
if(isset($_POST['type'])){
  $thread_id = $_POST['thread_id'];
  $threadCon = new Bbs\Controller\Thread();
  $threadCon->outputCsv($thread_id);
  exit();
}
else{
  header('Locatio: '.SITE_URL . '/thred_all.php');
  exit();
}
?>