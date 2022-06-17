<?php

namespace Bbs\Model;

class Thread extends \Bbs\Model
{
  public function createThread($values)
  {


    // ☆☆☆　トランザクション処理はすべて「try（例外処理）」の中に書かれている。　☆☆☆
    // スレッドのテーブルは情報を取得できたのに、コメントのテーブルは情報を取得できなかった、となる場合に、片方だけ
    // 登録されるのはおかしいため、例外処理でトランザクションとしてまとめている。
    try {


      // 『トランザクション』
      // 途中で中断されたくない「複数のSQL文を実行する」為の仕組み。
      // PHPで予め用意してくれているトランザクション処理を利用する。

      //  「$this」親クラスにある変数。
      // 「db」　Model.phpでデータベースを扱う処理が『PDO』がまとめられており、インスタンス化して変数「db」にしまわれている。
      // 「beginTransaction()」を開始しますよ、というPHPの関数。以降の処理がトランザクションになる。
      $this->db->beginTransaction();


      // 「threadsテーブル」にスレッドの情報を挿入するSQL文。
      $sql = "INSERT INTO threads (user_id,title,created,modified) VALUES (:user_id,:title,now(),now())";
      $stmt = $this->db->prepare($sql);

      // SQLで使われているバインド変数「:user_id、:title」に値を代入している。
      // 日本語に直すと、「フォーム入力した情報を、そのままデータベースに登録する情報として扱う」ということになる。
      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('title', $values['title']);
      $res = $stmt->execute();


      // 「lastInsertId()」 ⇒　最後にINSERTで挿入されたIDの値の取得をする。PHPで予め用意された関数。
      //  $thread_idに保存されている。コメントテーブルに登録するために必要な情報をここで準備している。
      // どのスレッドにコメントを投稿したかを判別するため。（あるスレッドに投稿したコメントを紐づけるため）
      $thread_id = $this->db->lastInsertId();


      // 「commnets」テーブルにコメント情報を挿入するSQL文。
      // 一つのスレッドに多くのコメント押し寄せる場合があるので、SQL文を2つに分ける。
      // 「スレッド”新規”作成」のINSERT文なので、comment_num（コメント件数）は必ず「1」となる。
      // ※新規作成すると、かならず初コメになるため。
      $sql = "INSERT INTO comments (thread_id,comment_num,user_id,content,created,modified) VALUES (:thread_id,1,:user_id,:content,now(),now())";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('thread_id', $thread_id);
      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('content', $values['comment']);
      $res = $stmt->execute();


      // 「commit」　⇒　トランザクションのすべての処理がうまく言ったら、「commit」で処理を確定する。
      $this->db->commit();
    }


    // トランザクション処理で例外が起きた場合に、「catch」で「rollBack」を行う。
    catch (\Exception $e) {
      echo $e->getMessage();


      // 「rollBack」　⇒　トランザクション処理を行う前に戻す。
      $this->db->rollBack();
    }
  }



  // 全スレッド取得
  public function getThreadAll()
  {
    // 「query」　バインド変数を使わない場合は、queryでSQL文を実行。
    $stmt = $this->db->query("SELECT id, title, created FROM threads where delflag = 0 order by id desc");



    // $stmt->fetchAll(\PDO::FETCH_OBJ)は何を表しているか。
    // var_dump($stmt->fetchAll(\PDO::FETCH_OBJ));
    // exit;


    //  $stmt⇒SQL文の実行結果。
    // 「fetchAll」　⇒　SQL文で取得してきた結果を、「全件（複数行）」取得の意味。
    // 「return」で呼び出し元に処理の結果を返却している。

    // 「PDO::FETCH_OBJ」⇒列名をプロパティに持つ匿名オブジェクト (stdClass) を返す。

    // 「stdClass」⇒　プロパティやメソッドを一切持たない標準クラス（デフォルトで用意されているクラス）。PHPでは全てのクラスの基本となるstdClassというクラスが存在する。
    // 通常、クラスを使う場合は何か定義しないといけないように思いますが、次のように、何も定義せずともstdClassを書くことが出 来る。。
    // stdClassはあらかじめ自分で定義されていないにも関わらず、そのまま使えます。これはPHPの内部であらかじめ定義されているから。
    // データを配列ではなく、オブジェクトの形で保存したい場合などに使うことが出来る。
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // 「::」スコープ定義演算子
  // クラスのプロパティとメソッドにアクセスする時にはスコープ定義演算子::を使う。
  // 「クラス名」　::　「呼び出したいクラスのプロパティまたはメソッド名」




  // コメント取得
  public function getComment($thread_id)
  {
    $stmt = $this->db->prepare("SELECT comment_num, username, content, comment.created FROM comments INNER JOIN users on user_id = user.id WHERE thread_id = :thread_id AND comments.delflag = 0 ORDER BY comment_num ASC LIMIT 5;");
    $stmt->execute([':thread_id' => $thread_id]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }
}
