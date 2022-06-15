<?php

//ini_set PHPの予め用意された関数。PHPのいろいろな設定を行うことが出来る。
// display_errors
// 1(true)又は0(false)で、PHPエラーの画面表示・非表示を設定できる。
// エラーが出ると、開発のヒントになるのでエラー画面を”わざ”と出すようにしている。
// ※ローンチするときには外す。
ini_set('display_errors',1);

// PHPの構文
// define('定数名', '値');
define('DSN','mysql:host=localhost;charset=utf8;dbname=bbs');
define('DB_USERNAME','bbs_user');
define('DB_PASSWORD','QbSJHLeRgMPgnUe7');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/bbs/public_html');
require_once(__DIR__ .'/../lib/Controller/functions.php');
require_once(__DIR__ . '/autoload.php');
session_start();