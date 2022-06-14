<?php
namespace Bbs;
class Controller {
  private $errors;
  private $values;


// 「__construct関数」（PHPで予め用意されている関数）
// インスタンスを行った時点で、『自動的に』実行される。
// 今回はSignupクラス（継承されている親クラス「Controller」を含め）インスタンス化をした時に、自動で処理が実行される。
  public function __construct() {



    // CSRF対策 推測されにく文字列を生成
    // $_SESSION['token']の[]内の名前は自由に（好きなものを）付けることが出来る。
    if (!isset($_SESSION['token'])) {


      // bin2hex(openssl_random_pseudo_bytes(16)　⇒　PHPで用意されている関数。
      // 16文字（16byte）のランダムな英数字の生成。
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }
    // PHPデフォルトクラス 宣言なしでインスタンス生成ができる
    // オブジェクト型のデータを作る際に使う
    $this->errors = new \stdClass();
    $this->values = new \stdClass();
  }



  // 入力エラーの場合に画面上に値を残したままにする際に使用
  protected function setValues($key, $value) {
    $this->values->$key = $value;
  }
  // 入力エラーの場合に画面上に値を残したままにする際に使用
  public function getValues() {
    return $this->values;
  }
  protected function setErrors($key, $error) {
    $this->errors->$key = $error;
  }
  public function getErrors($key) {
    return isset($this->errors->$key) ? $this->errors->$key : '';
  }
  // エラーチェック判定メソッド
  protected function hasError() {
    // get_object_vars関数→指定したオブジェクトのプロパティを取得する
    return !empty(get_object_vars($this->errors));
  }
  // ログイン確認メソッド
  protected function isLoggedIn() {
    return isset($_SESSION['me']) && !empty($_SESSION['me']);
  }
}