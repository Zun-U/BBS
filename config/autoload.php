<?php
// プログラム上で未定義のクラスが見つかったら spl_autoload_register で定義した内容に従って自動的にファイルを require するオートロードの設定



// 『オートロードの設定』
// クラスのインスタンス化を記述をする”前”に　「requierでそのクラスを読み込む」という作業がインスタンス化のたびに必要になる。
// それを自動化したもの。
spl_autoload_register(function($class) {
  $prefix = 'Bbs\\';
  if (strpos($class, $prefix) === 0) {
    $className = substr($class, strlen($prefix));
    $classFilePath = __DIR__ . '/../lib/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classFilePath)) {
      require $classFilePath;
    }
  }
});
