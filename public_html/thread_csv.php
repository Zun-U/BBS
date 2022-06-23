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


<!-- CSV -->
<!-- カンマ(,)で区切ったデータ形式のこと -->
<!-- データベースに登録し易いデータ形式 -->
<!-- CSVのデータをもとにテーブルを作成したりする。 -->
<!-- CSVをエクセルの形式で確認することができる。 -->
<!-- CSV ⇒　「shift JIS」という文字規格に
なっている為、VScodeで開くと文字化けが起こる。 -->