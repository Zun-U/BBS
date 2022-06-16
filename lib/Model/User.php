<?php

namespace Bbs\Model;

class User extends \Bbs\Model
{


  // 「$values」　Signup.phpでフォームの入力値が引数として渡されている。
  public function create($values)


  // var_dump()の（）の中に$valuesを入れると、中身の内容がわかる。（JSのconsole.log()みたいなもの）
  {

    // 「db」はデータベースに接続するための処理がまとめられているメンバ変数。ここでは「Model」クラス固有の変数として定義sれている。
    // 下のSQL文を実行するにはデータベースへの接続が必要になる。その為、「Model」クラスを継承して、DB接続するための「定数$db」を使えるようにしている。

    //  データベースにINSERT文でユーザー登録する。
    $stmt = $this->db->prepare("INSERT INTO users (username,email,password,created,modified) VALUES (:username,:email,:password,now(),now())");
    $res = $stmt->execute([
      ':username' => $values['username'],
      ':email' => $values['email'],

      // パスワードのハッシュ化
      // 「password_hash」　PHPに用意されている命令。パスワードをそのままデータベースに登録しないで、暗号化をしている。
      ':password' => password_hash($values['password'], PASSWORD_DEFAULT)
    ]);

    // メールアドレスがユニークでなければfalseを返す
    // （メールアドレスがすでに登録されている場合）
    if ($res === false) {
      var_dump($values);
      throw new \Bbs\Exception\DuplicateEmail();
    }
  }


  // ログイン機能　関数名「login」
  public function login($values)
  {



    // ☆☆　『prepare』　☆☆
    // この実行するSQLを「準備」し、後からSQLを実行することをプリペアードステートメントと言います。
    // SQL文にユーザーの入力値をそのままセットし、実行してしまうと、アプリケーションが想定しないSQL文を実行させることにより、
    // データベスを不正に操作できてしまいます。これをSQLインジェクションと言います。
    // SQLインジェクション対策で、プリペアードステートメントを行っています。

    //  prepare()の中に書かれたSQL文に「変数」が用いられている場合は、データベースが「解釈できる形」に一旦直す。
    // ()の中に、データベースが実行できるSQL文として保存する。
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email;");

    //  「execute」　⇒　プリペアドステートメントを実行する
    $stmt->execute([



      // 「バインド変数」⇒ SQL文に埋め込む変数のこと。
      // 必ずコロン(:)から書き始める。
      // 安全性を持たせるため。
      // 実際の値を後から設定できるように、「SQL文の一部を変数」にしておくことがある。そのときの変数のこと。
      // バインド変数を使うメリットは、会話の量が減ることによる効率化。☆データベースさんが楽をできます。☆
      ':email' => $values['email']

      // 「=>（ダブルアロー演算子）」
      // 連想配列のkeyとそれに紐づく値の関係を表すもの。
      // ':email'がkeyで$values['email']が値。
      // つまり今回では、「:email」keyにはユーザーが入力した「email」の値が紐づくという意味になる。
      // ここでは「$values」はSignup.phpで「関数create」で実行されたユーザーのフォーム入力情報となる。
    ]);




    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    // $user 変数にSQL文を実行し取得した値（配列）を格納している。
    $user = $stmt->fetch();


    // ちゃんとユーザー登録がされていたら、$userに情報がある。
    // var_dump($user);
    // exit;


    // 「empty()」⇒カッコの中が空かどうか。　空なら(値が何もない場合)「ture」、空でなければ「false」
    if (empty($user)) {
      throw new \Bbs\Exception\UnmatchEmailOrPassword();
    }
    if (!password_verify($values['password'], $user->password)) {
      throw new \Bbs\Exception\UnmatchEmailOrPassword();
    }
    return $user;
  }
}
