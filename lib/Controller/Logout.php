<?php
namespace Bbs\Controller;
class Logout extends \Bbs\Controller {
  public function run() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        echo "不正なトークンです!";
        exit();
      }

     // セッション変数に入ってるユーザー情報を、空の配列（[]）で上書きしている。
      $_SESSION = [];
      if (isset($_COOKIE[session_name()])) {

        // 「session_name」　現在のセッション名を取得または設定する。phpに用意されている関数。
        // 「setcoolie」　クッキーを設定する。
        // つまりここでは、空にしたセッションのクッキーの設定をしている。
        // ・time ⇒　有効期限の設定。現在の日付からかなり過去（- 86400）に設定することで、強制的に有効期限切れにして、クッキーの破棄を行わせる。つまり、クッキーの削除を行う設定になる。
        setcookie(session_name(), '', time() - 86400, '/');
      }
      // セッションの破棄
      // 全てを空にした上で、セッションを破棄する。（空でないと、ずっとログインしたままになる。）
      session_destroy();
    }
    // トップページへリダイレクト
    // セッションの値が空になるため、セッションの値が空のときのヘッダーのメニューになる。
    header('Location: ' . SITE_URL);
  }
}