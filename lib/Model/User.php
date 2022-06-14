<?php

namespace Bbs\Model;

class User extends \Bbs\Model
{

  // 「$values」　Signup.phpでフォームの入力値が引数として渡されている。
  public function create($values)


  // var_dump()の（）の中に$valuesを入れると、中身の内容がわかる。（JSのconsole.log()みたいなもの）
  {



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
public function login($values) {
  $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email;");
  $stmt->execute([
    ':email' => $values['email']
  ]);
  $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
  $user = $stmt->fetch();
  if (empty($user)) {
    throw new \Bbs\Exception\UnmatchEmailOrPassword();
  }
  if (!password_verify($values['password'], $user->password)) {
    throw new \Bbs\Exception\UnmatchEmailOrPassword();
  }
  return $user;
}
}
