<?php

namespace Bbs\Controller;

class UserUpdate extends \Bbs\Controller
{

  public function run()
  {
    $this->showUser();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->updateUser();
    }
  }


  // 他ページからマイページにアクセスした時の挙動
  protected function showUser()
  {
    $user = new \Bbs\Model\User();

    // 「User.php」で定義した「find」メソッド（現在ログインしているユーザーIDを使って、そのIDに紐づくユーザー情報をセレクト文で全件検索した結果が格納されている変数。）
    $userData = $user->find($_SESSION['me']->id);

    // 取得してきた情報を「Controller」の「setValuesメソッド」を利用して、「getValuesメソッド」で画面に表示する為の準備をしている。
    $this->setValues('username', $userData->username);
    $this->setValues('email', $userData->email);
    $this->setValues('image', $userData->image);
  }


  protected function updateUser()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\InvalidEmail $e) {
      $this->setErrors('email', $e->getMessage());
    } catch (\Bbs\Exception\InvalidName $e) {
      $this->setErrors('username', $e->getMessage());
    }

    $this->setValues('username', $_POST['username']);
    $this->setValues('email', $_POST['email']);
    if ($this->hasError()) {
      return;
    } else {



      // アップロードする画像の登録処理
      // 『$_FILES[]』　⇒　「input type="file"」で送信されてきたデータを取得できる。
      // []の中はname属性。今回は「name="image"」のもの。
      $user_img = $_FILES['image'];
      // var_dump($_FILES['image']);
      // exit;


      // 変更前の画像を取得
      // 『$_POST』　⇒　ここでは、「mypage.php」から「old_image」のname属性がついた<input>タグのフォーム送信されてくるデータを取得してきている。
      $old_img = $_POST['old_image'];


      // データベースに変更前の画像がない場合、「NULL」を代入する。
      if($old_img == '') {
        $old_img = NULL;
      }


      // アップロードされてきた画像の名前をこちらで「ユニーク（ランダム）」な名前に変更する。
      // ★画像名がかぶらないほうが管理しやすいため。★

      //アップロードされてきたファイルの拡張子の部分を抜き取ってきている。
      $ext = substr($user_img['name'], strrpos($user_img['name'], '.') + 1);

      // 『uniqid』　⇒　ユニークなファイル名を付けている。
      // '.'　⇒　拡張子とファイル名の区切り、ドット(.)。
      //  $ext ⇒　上記で抽出してきた拡張子部分が格納されている変数。。
      // 出来上ったものは「img__ユニークな名前.抽出した拡張子」
      $user_img['name'] = uniqid("img__") . '.' . $ext;




      try {
        $userModel = new \Bbs\Model\User();

        // アップロードされてきたファイルには「size」情報がもともとある。その「size」情報をが0より大きければ、言い換えるとアップロードされてきたファイルが存在すれば、という意味になる。
        if ($user_img['size'] > 0) {

          // 「unlink」 => 指定した今までつかっていた古い画像を画像フォルダを削除する。PHPで用意された関数。
          unlink('./gazou/' . $old_img);


          //  「move_uploaded_file(第一引数、第二引数)」⇒　どこどこのフォルダ（ファイル）に指定したファイルを移動する。
          // 第一引数のファイルを、第二引数の場所へ移動する。

          // 「$user_img['tmp_name']」ファイルを「'./gazou/' . $user_img['name']」に移動する。
          // 画像をアップロードする時に、一時的に保存してから使うという流れがある。

          move_uploaded_file($user_img['tmp_name'], './gazou/' . $user_img['name']);


          // 画像の移動が発生した場合
          $userModel->update([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'userimg' => $user_img['name']
          ]);
          $_SESSION['me']->image = $user_img['name'];
        } else {

          // 画像の処理（移動）が伴わない場合
          $userModel->update([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'userimg' => $old_img
          ]);
          $_SESSION['me']->image = $old_img;
        }
      } catch (\Bbs\Exception\DuplicateEmail $e) {
        $this->setErrors('email', $e->getMessage());
        return;
      }
    }
    $_SESSION['me']->username = $_POST['username'];
    header('Location: ' . SITE_URL . '/mypage.php');
    exit();
  }

  private function validate()
  {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです！";
      exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \Bbs\Exception\InvalidEmail("メールアドレスが不正です！");
    }
    if ($_POST['username'] === '') {
      throw new \Bbs\Exception\InvalidName("ユーザー名が入力されていません！");
    }
  }
}
