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

    $user_id = $_SESSION['me']->id;

    // 「query」　バインド変数を使わない場合は、queryでSQL文を実行。

    // 「LEFT JOIN」でテーブルを結合するが、同名の列がある場合は結合できないため、カスタムデータ属性で名前を異なるようにしている。
    // (t_id、f_idなど)
    $stmt = $this->db->query("SELECT t.id AS t_id, title, t.created, f.id AS f_id FROM threads AS t LEFT JOIN favorites AS f ON t.delflag = 0 AND t.id = f.thread_id AND f.user_id = $user_id ORDER BY t.id desc");


    // 「LEFT　JOIN」である理由　⇒　スレッドの全件を表示したい、『お気に入りであるがなかろうが』。
    // 結合の条件が一致しなくてもとりあえず「threads」の情報が全件表示される。



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

  // CSV出力
  public function getCommentCsv($thread_id){$stmt = $this->db->prepare("SELECT comment_num, username, content, comments.created FROM (threads INNER JOIN comments ON threads.id = comments.thread_id) INNER JOIN users ON comments.user_id = users.id WHERE threads.id = :thread_id AND comments.delflag = 0 ORDER BY comment_num ASC;");
    $stmt->execute([':thread_id' => $thread_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  // コメント数取得
  public function getCommentCount($thread_id)
  {
    $stmt = $this->db->prepare("SELECT COUNT(comment_num) AS record_num FROM comments WHERE thread_id = :thread_id AND delflag = 0;");
    $stmt->bindValue('thread_id', $thread_id);
    $stmt->execute();


    // FETCH_ASSOCは取得結果を連想配列で返す。
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['record_num'];
  }

  // スレッド1件取得
  public function getThread($thread_id)
  {
    $stmt = $this->db->prepare("SELECT * FROM threads WHERE id = :id AND delflag = 0;");
    $stmt->bindValue(":id", $thread_id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_OBJ);
  }


  // コメント全件取得
  public function getCommentAll($thread_id)
  {
    $stmt = $this->db->prepare("SELECT comment_num, username, content, comments.created FROM comments INNER JOIN users ON user_id = users.id WHERE thread_id = :thread_id AND comments.delflag = 0 ORDER BY comment_num ASC;");
    $stmt->execute([':thread_id' => $thread_id]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  public function createComment($values)
  {
    try {
      $this->db->beginTransaction();
      $lastNum = 0;

      // comment_numの一番大きい行のcomment_numの値の取得
      // desc   ＝　降順 （編集済み
      // LIMIT　＝　何行分だけ、という制約
      // つまり、一番後ろから（大きい数字から）１行分だげ　⇒　最新の投稿されたコメントカウントだけ。
      $sql = "SELECT comment_num FROM comments WHERE Thread_id = :thread_id ORDER BY comment_num DESC LIMIT 1";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('thread_id', $values['thread_id']);
      $stmt->execute();
      $res = $stmt->fetch(\PDO::FETCH_OBJ);




      // $lastNum
      $lastNum = $res->comment_num;

      // データベースの値を取得してきて、その値をPHP側で「＋１」している。
      $lastNum++;



      $sql = "INSERT INTO comments (thread_id, comment_num, user_id, content, created, modified) VALUES(:thread_id, :comment_num, :user_id, :content, now(), now())";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('thread_id', $values['thread_id']);


      // $lastNumに代入された値（★+1されている★）を下記でcomment_numにセットしている。
      $stmt->bindValue('comment_num', $lastNum);



      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('content', $values['content']);
      $stmt->execute();
      $this->db->commit();
    } catch (\Exception $e) {
      echo $e->getMessage();
      $this->db->rollBack();
    }
  }


  public function changeFavorite($values)
  {
    try {
      $this->db->beginTransaction();
      // レコード取得
      // お気に入り（favorites）テーブルにデータが登録されているかどうかを調べる。
      $stmt = $this->db->prepare("SELECT * FROM favorites WHERE thread_id = :thread_id AND user_id = :user_id");
      $stmt->execute([
        ':thread_id' => $values['thread_id'],
        ':user_id' => $values['user_id']
      ]);
      $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

      // $rec  ⇒　上記SELECT文の結果が１行で保存される。
      $rec = $stmt->fetch();


      $fav_flag = 0;

      //  $rec  ⇒　上記SELECT文の結果
      // empty($rec)　⇒　その結果が空であれば。（ここではお気に入りテーブルに登録されていなければ）
      if (empty($rec)) {

        // データに何も登録されていないので、新規のお気に入り登録。
        $sql = "INSERT INTO favorites (thread_id,user_id,created) VALUES (:thread_id,:user_id,now())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
          ':thread_id' => $values['thread_id'],
          ':user_id' => $values['user_id']
        ]);
        $fav_flag = 1;
      }

      // すでにお気に入り登録されていれば、テーブルから削除。
      else {
        $sql = "DELETE FROM favorites WHERE thread_id = :thread_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
          ':thread_id' => $values['thread_id'],
          ':user_id' => $values['user_id']
        ]);
        $fav_flag = 0;
      }
      $this->db->commit();

      // 呼び出し元に「$fav_flag;」を返している。　⇒　呼び出し元はAjax.php
      return $fav_flag;
    } catch (\Exception $e) {
      echo $e->getMessage();
      // エラーがあったら元に戻す
      $this->db->rollBack();
    }
  }




  // お気に入りの中の全スレッド取得
  public function getThreadFavoriteAll()
  {
    $user_id = $_SESSION['me']->id;
    $stmt = $this->db->query("SELECT t.id AS t_id, title, t.created, f.id AS f_id FROM threads AS t INNER JOIN favorites AS f ON t.delflag = 0 AND t.id = f.thread_id AND f.user_id = $user_id ORDER BY t.id desc");

    //  「threads AS t」　⇒　「threads」テーブルを「t」という別名を付けている。
    //  「 AS f」 favorites⇒　「favorites」テーブルを「f」という別名を付けている。
    //  「t.id AS t_id」　⇒　「threads」テーブルの「id」列を「t_id」と別名を付けている。

    //  どのテーブルの列なのかを判別するために、テーブルに別名を付けている。　

    // 「t.id = f.thread_id」　⇒　「threads」テーブルの「id」　と　「favorites」テーブルの「thread_id」が一致するもの。

    // 「f.user_id = $user_id」　⇒　「favorites」テーブルの「user_id」と「$user_id」（★ログインしているユーザーのID★）が一致するもの。
    // ログインしているユーザーが必要。



    // 「INNER JOIN」 である理由　⇒　結合の条件が一致するもののみ表示される。※お気に入り登録一覧のみを表示したいため。


    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }
}
