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
      ':password' => password_hash($values['password'], PASSWORD_DEFAULT)
    ]);
    // メールアドレスがユニークでなければfalseを返す
    if ($res === false) {
      throw new \Bbs\Exception\DuplicateEmail();
    }
  }
}
