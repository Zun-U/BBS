<?php

namespace Bbs\Controller;
// Controllerクラス継承
// 『extends』　継承
// 自分自身に書かれている処理の内容と、親クラス（今回はControllerクラス）に書かれている処理の内容を可能とする。
// 継承元の親クラスにはよく使う処理がまとめられている。
class Signup extends \Bbs\Controller
{

  // １回目はページが読み込まれた時に「run」のメソッドが実行される。
  // ２回目はフォーム送信をした時に。（登録ボタンを押して”自分自身”にフォーム送信され、再読み込みが行われた時）
  public function run()
  {
    if ($this->isLoggedIn()) {
      header('Location: ' . SITE_URL);
      exit();
    }


    // POSTメソッドがリクエストされていればpostProcessメソッド実行。
    // ”フォーム送信によって”この「run」が実行された時に、という意味。
    // つまり、どういう流れでこのページにたどり着いたかをチェックしている。
    // 「REQUEST_METHOD」　どういう方法でこのページに来たか、というPHPで予め用意された文言。今回は「POST」送信形式に合致すれば、という処理になっている。
    // アクセスの種類によって処理を分けることを「rooting（ルーティング）」と言う。
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->postProcess();
    }
  }




  protected function postProcess()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\InvalidEmail $e) {
      $this->setErrors('email', $e->getMessage());
    } catch (\Bbs\Exception\InvalidName $e) {
      $this->setErrors('username', $e->getMessage());
    } catch (\Bbs\Exception\InvalidPassword $e) {
      $this->setErrors('password', $e->getMessage());
    }




    // 　セッター（setValues） と　ゲッター（getValues）。
    // 　「値をセットするもの」 と　「値をゲットするもの」。
    // 「setValues」で取得してきた値を保存、「getValues」で保存した値を取得する。
    // これらはControllerクラスに書かれている。
    $this->setValues('email', $_POST['email']);
    $this->setValues('username', $_POST['username']);


    // 例外処理が発生したかどうかを調べるための関数。
    if ($this->hasError()) {

      // 例外処理が発生した場合
      // これ以上の処理を実行しない「return」という処理を行っている。
      return;

      // エラーがなかった場合の処理
    } else {



      // 「try」 予期せぬエラー
      // 例外が発生する可能性のある処理をここに記述する。
      try {
        $userModel = new \Bbs\Model\User();


        // 「create」　登録処理
        // $_POST['email']　⇒　フォームの入力値を表している。ここではcreate()の（）内の引数として渡している。
        $user = $userModel->create([
          'email' => $_POST['email'],
          'username' => $_POST['username'],
          'password' => $_POST['password']
        ]);
      }


      // 「catch」予期せぬエラーが起きたときに実行する処理
      // 例外が発生した時に実行する特別な処理をここに記述する。
      catch (\Bbs\Exception\DuplicateEmail $e) {
        $this->setErrors('email', $e->getMessage());
        return;
      }
    }

    // ToDo:ユーザー登録後、ログイン処理を行う
  }

  // バリデーションメソッド
  private function validate()
  {
    // トークンが空またはPOST送信とセッションに格納された値が異なるとエラー
    //　送信されてきた内容にトークンがセットされてきていること。
    // $_POST['token'] !== $_SESSION['token']　⇒　インスタンス化した際に発生したトークンとフォーム送信されてきたトークンが一致するかどうかのチェック。セキュリティに関するバリデーション。
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }

    // こちら側で用意したフォーム欄で入力されたかどうかのチェック。
    if (!isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['password'])) {
      echo "不正なフォームから登録されています!";
      exit();
    }


    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {


      // 「throw」で”わざと”例外処理の発生
      // 予期せぬエラーではなく、『あらかじめ例外を作っておいて』”予期した”例外エラーを発生させている。
      throw new \Bbs\Exception\InvalidEmail("メールアドレスが不正です!");
    }



    // 「$_POST['username'] === ''」　　⇒　　入力されたユーザー名が空であれば、という条件。
    if ($_POST['username'] === '') {

      // InvalidNameクラスのインスタンス化、throwで例外処理の実行
      throw new \Bbs\Exception\InvalidName("ユーザー名が入力されていません!");
    }



    // 正規表現と合うかどうかのバリデーションチェック（'/\A[a-zA-Z0-9]+\z/'　大小英文字と数字のみという縛り）
    if (!preg_match('/\A[a-zA-Z0-9]+\z/', $_POST['password'])) {
      throw new \Bbs\Exception\InvalidPassword("パスワードが不正です!");
    }
  }
}

// 「try」「throw」「catch」の順番で実行される。