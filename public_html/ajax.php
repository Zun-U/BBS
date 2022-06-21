<?php
require_once(__DIR__.'/../config/config.php');

$threadApp = new \Bbs\Model\Thread();
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  try{
    $res = $threadApp->changeFavorite([
      'thread_id' => $_POST['thread_id'],
      'user_id' => $_POST['user_id']
    ]);
    header('Content-type: application/json');
    echo json_encode($res);
  }catch(Exception $e){
    header($_SERVER['SERVER_PROTCOL'].'500 Internal Server Error', true, 500);
    echo $e->getMessage();
  }
}
?>