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
      // var_dump($values);
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

    // 退会済みのユーザーであればログインできなくする。
    // $userには『オブジェクト形式』で連想配列の値が格納されているため、値を取り出すにはアロー演算子でなければいけない。
    if ($user->delflag === '1') {
      throw new \Bbs\Exception\DeleteUser();
    }

    // var_dump($user->delflag);
    // exit;
    return $user;
  }

  // 引数として渡されたidに紐づいたユーザー情報をSELECT文で取得し、配列（1行）の形で $userにしまっている。
  public function find($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id;");
    $stmt->bindValue('id', $id);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    return $user;
  }


  // データベースでは画像はそのものではなく、ファイル『名』で保存する。※画像そのものは別で保存する。
  // このユーザーさんはこの画像『名』の画像ファイルを使っているということがわかれば、表示させることができる。
  public function update($values)
  {
    $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, image = :image, modified = now() where id = :id");
    $stmt->execute([
      ':username' => $values['username'],
      ':email' => $values['email'],
      ':image' => $values['userimg'],
      ':id' => $_SESSION['me']->id,
    ]);
    if ($res === false) {
      throw new \Bbs\Exception\DuplicateEmail();
    }
  }

  // 現在ログインしているユーザーに紐づくIDを検索条件に、「Users」の「delflag」の値を「１」にUPDATE文で書き換え、論理削除を行っている。
  public function delete()
  {
    $stmt = $this->db->prepare("UPDATE users SET delflag = :delflag, modified = now() WHERE id = :id");
    $stmt->execute([

      // 「=>（ダブルアロー演算子）」　⇒　”連想配列”のこの「キー」にこの「値」を入れる、というもの。配列に値を代入するための演算子。
      ':delflag' => 1,
      ':id' => $_SESSION['me']->id,
    ]);
  }
}
