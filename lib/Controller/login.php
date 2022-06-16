<?php

namespace Bbs\Controller;

class Login extends \Bbs\Controller
{
  public function run()
  {
    // ログインしていればトップページへ移動
    // if ($this->isLoggedIn()) {
    //   header('Location: ' . SITE_URL);
    //   exit();
    // }


    // postProcess
    // 一つのクラスの中では一つのメソッド。
    // クラスの中でメソッドという処理の単位で書かれている。
    // Loginクラスの中に書かれている postProcessメソッド。
    // ※Signupクラスにも postProcessメソッドが使われているが、別クラスなので問題ない。
    // （理由は、先にクラスのインスタンス化が行われているから、インスタンスが行われているpostProcessメソッドを使う。）

    // ※Signupクラスも以下コードと似たような書き方になっているが、できるだけ似ている方が良い。（可読性、作業効率の観点から）

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->postProcess();
    }
  }

  // 『$this』　⇒　 疑似変数thisとは、クラスのインスタンス自身のことを指すもの。
  // クラスを実装する時に「自身のプロパティやメソッド」に「アクセスするため」に使用する。
  // クラス定義内部であればアクセスできるオブジェクト（インスタンスメソッド）。
  // $thisを使うことで（クラス内であれば）どこでも呼び出せる変数になっている。
  // echo $this->fruit;は「クラス内で定義されているfruit変数をechoするよ」という意味になる。

  // 「->」アロー演算子
  // 「あるクラスのインスタンス」 -> 「そのクラスのプロパティ・メソッド」
  // 左辺のクラスに格納されている関数や変数を『呼び出し』・『実行』するもの。

  // アクセス修飾子　private,protected,public
  // そのクラスに書かれているメソッドを使いたい場合は、そのクラスをインスタンス化する必要がある。
  // protected: 自分のクラス、もしくは親クラスならアクセスできる、というアクセス制限。

  protected function postProcess()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('login', $e->getMessage());
    }

    // $_POST['email']の定義
    $this->setValues('email', $_POST['email']);
    if ($this->hasError()) {
      return;
    } else {
      try {
        $userModel = new \Bbs\Model\User();
        $user = $userModel->login([
          'email' => $_POST['email'],
          'password' => $_POST['password']
        ]);
      } catch (\Bbs\Exception\UnmatchEmailOrPassword $e) {
        $this->setErrors('login', $e->getMessage());
        return;
      } catch (\Bbs\Exception\DeleteUser $e) {
        $this->setErrors('login', $e->getMessage());
        return;
      }
      // ログイン処理
      //session_regenerate_id関数･･･現在のセッションIDを新しいものと置き換える。セッションハイジャック対策
      // セッションには基本的には個人情報が載っているので、IDを更新して外部から取得されにくくしている。
      session_regenerate_id(true);

      // ユーザー情報をセッションに格納
      // 「$_SESSION['me']」という変数にユーザー情報を格納する理由は、使い回せるようになるため。ユーザー情報が欲しい時に色々な場所で使う。
      $_SESSION['me'] = $user;


      // スレッド一覧ページへリダイレクト
      //  「header関数」　⇒　HTTPヘッダの内容を指定できる。主に指定したページにリダイレクトの用途。
      // 　'Location: 'を最初に書くのがルール。
      header('Location: ' . SITE_URL . '/thread_all.php');
      exit();
    }
  }
  private function validate()
  {

    // トークンが空またはPOST送信とセッションに格納された値が異なるとエラー
    //     var_dump($_POST['token']);//post送信されたトークン（送っれてきたトークン）
    // echo '<br>';
    //     var_dump($_SESSION['token']);//ページに遷移した時点で付与されるトークン
    //     exit;

    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "トークンが不正です!";
      exit();
    }
    // emailとpasswordのキーがなかった場合、強制終了
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
      echo "不正なフォームから登録されています!";
      exit();
    }
    if ($_POST['email'] === '' || $_POST['password'] === '') {
      throw new \Bbs\Exception\EmptyPost("メールアドレスとパスワードを入力してください!");
    }
  }
}
