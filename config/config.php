<?php

// すべてのファイルでよく使うファイルがここでまとめられている。（基本設定などの情報）
// 今回は、ヘッダーが読み込まれているページであれば、config.phpのファイルが読み込まれている。




//ini_set PHPの予め用意された関数。PHPのいろいろな設定を行うことが出来る。
// display_errors
// 1(true)又は0(false)で、PHPエラーの画面表示・非表示を設定できる。
// エラーが出ると、開発のヒントになるのでエラー画面を”わざ”と出すようにしている。
// ※ローンチするときには外す。
ini_set('display_errors', 1);

// PHPの構文
// define('定数名', '値');
define('DSN', 'mysql:host=localhost;charset=utf8;dbname=bbs');
define('DB_USERNAME', 'bbs_user');
define('DB_PASSWORD', 'QbSJHLeRgMPgnUe7');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/bbs/public_html');
require_once(__DIR__ . '/../lib/Controller/functions.php');
require_once(__DIR__ . '/autoload.php');




// セッション変数とは、サーバー上に「ユーザー専用のデータの保管領域」を確保する仕組み。
// サーバー上に「ユーザー専用のデータの保管領域」を確保する仕組みです。
// hiddenは「各ユーザーの画面内（HTML）」にデータを保持していたのに対し、セッション変数は「サーバー内」にデータが保存される。
// サーバーは多くのユーザーが同時に使っていますので、自分の保管領域にデータを出し入れするための「専用のキー」が発行される。
// これを「セッションID」と言います。

// どのページにも使う部分（今回はconfig）でセッションスタートしている。
//　☆☆つまりどこても「セッション変数」が使えますよ、という環境をセットしている。

// session_start()関数は、サーバー側では、指定されたディレクトリにセッション変数を保存するファイルが作成される。
// 「session_start」がないと「セッション」機能が使えない。
// ユーザー情報はログインしてからログアウトするまで、基本的に「セッション」に保存されている。
// Coockieは「ブラウザ上（利用者のパソコン）」に保存される一時的な情報。SESSIONは「サーバー上」に保存されている一時的な情報。
// このような情報は、悪意ある者のなりすましを防ぐために使われている。
session_start();





// 非ログイン時のリダイレクト処理

// 『$_SERVER』　⇒　サーバー情報、および実行時の環境情報を取得するPHPのメソッド
//  インデックス『REQUEST_URI』　⇒　ページにアクセスするために指定された URI。例えば、 '/index.html'。
$current_uri = $_SERVER["REQUEST_URI"];


// 『basename』　⇒　()内で指定されたページのファイル名を取得する。ここでは現在表示されているページから、という意。
$file_name = basename($current_uri);


// var_dump($_SERVER["REQUEST_URI"]);
// echo '<br>';
// var_dump($file_name );
// exit;


//『strpos』　⇒　指定した文字列の中に、指定した文字が含まれているかどうかチェックするPHPの処理。
if(strpos($file_name, 'login.php') !== false || strpos($file_name,'signup.php') !== false || strpos($file_name,'index.php') !== false || strpos($file_name,'public_html') !== false){
  // URL内のファイル名がlogin.php、signup.php、index.php(public_html)のとき
  // IF文の中に何も処理がない　⇒　何もしない　⇒　ファイル名がlogin.php、signup.php、index.php(public_html)なら「何もしない」
}
else{
  // それ以外の時（上記以外のURL、またはよくわからないURLから飛んできた時）
  // 「!isset($_SESSION['me']」　⇒　ログインの有無判定。値がセットされていなければ、つまりログインされていなければ。
  if(!isset($_SESSION['me'])){

    // ヘッダー関数で指定した画面に変遷させる。⇒ここでは、login.phpに飛ばす。
    header('Location: '. SITE_URL . '/login.php');
    exit();
  }
}



// 『URI』は「Uniform Resource Identifier」の略で、『Web上にあるあらゆるファイルを認識するための識別子の総称』で、☆☆URNとURLで構成されています。☆☆
// 1つのファイルの「住所」を示すのがURL、「名前」を示すのがURNで、それらの総称がURIです。
// URLとURNはURIの枠組みの中にあるため、URL＝URIで、URN＝URIと考えても問題ないでしょう。




// ☆☆☆☆　
//「http:(もしくはhttps:)」は『URI側のパーツ』で、URLには含まれません。
// なので、「 https://ferret-plus.com/ 」は、正確には「URI」と呼ばれます。
// ☆☆☆☆