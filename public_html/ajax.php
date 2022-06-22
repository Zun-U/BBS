<?php
require_once(__DIR__ . '/../config/config.php');

$threadApp = new \Bbs\Model\Thread();

// Ajaxからpost送信されてきたら、if文以下を実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  try {
    // Model\Threadクラスの「changeFavorite」メソッドの実行
    // 「$fav_flag;」の値（1 or 0）も「$res」に格納されている。
    $res = $threadApp->changeFavorite([

      // Ajaxでpost送信されてきた値が、「changeFavorite」メソッドの引数として渡されている。
      // ここでは「スレッドID」と「ユーザーID」
      'thread_id' => $_POST['thread_id'],
      'user_id' => $_POST['user_id']
    ]);

    header('Content-Type: application/json');

    // 『json_encode』　⇒　「$res」にしまわれているデータを「json」の形式で出力せよ、というメソッド。
    // 理由はphpで処理した値をJSで使いたいから。（★PHPで処理したデータはJSで『使えない』★）
    // json形式ならJSで扱えるようになる。
    echo json_encode($res);




  } catch (Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    echo $e->getMessage();
  }
}
