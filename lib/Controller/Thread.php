<?php

namespace Bbs\Controller;

class Thread extends \Bbs\Controller
{
  public function run()
  {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {


      // $_POST['type']　⇒　「type」というname属性を持っているフォーム部品からPOST送信された値が、
      // 「createthread」という値と完全に一致すれば、という条件分岐。

      // つまり、「スレッド作成」ボタンを押したら、createThread（スレッド作成処理の関数名）を実行しなさい、の意。
      if ($_POST['type']  === 'createthread') {
        $this->createThread();
      } elseif($_POST['type'] === 'createcomment') {
        $this->createComment();
      }
      // 「run」の実行タイミングは２回ある。１回目はアクセスしたタイミング、２回目はフォーム送信したタイミング。
      // 『notice undefined index』エラーはPOST・GET送信の中身が何かわからない状態で発生する。
      // 「isset関数」でGET送信の中身の有無を確認している。
    } elseif(isset($_GET['type'])){
    if($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['type'] === 'searchthread') {
      $threadData = $this->searchThread();
      return $threadData;
    }
  }
  }

  private function createThread()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('create_thread', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrors('create_thread', $e->getMessage());
    }

    //  webアプリケーションはviewでフォーム送信して、controllerでバリデーションチェックして、チェックが通ったらModelに処理を渡して、という流れが基本になる。


    $this->setValues('thread_name', $_POST['thread_name']);
    $this->setValues('comment', $_POST['comment']);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createThread([

        // viewの「thread_name」
        'title' => $_POST['thread_name'],

        'comment' => $_POST['comment'],

        // $_SESSION['me']　⇒　ログインしたユーザー情報が保存されている。
        // 現在ログインしているユーザーの「usersテーブル」の「id」の情報。
        // どのユーザーがスレッドを作成したかを判別するため。
        'user_id' => $_SESSION['me']->id
      ]);
      header('Location: ' . SITE_URL . '/thread_all.php');
      exit();
    }
  }

  private function createComment()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('content', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrors('content', $e->getMessage());
    }
    $this->setValues('content', $_POST['content']);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createComment([
        'thread_id' => $_POST['thread_id'],
        'user_id' => $_SESSION['me']->id,
        'content' => $_POST['content']
      ]);
    }
    header('Location: ' . SITE_URL . '/thread_disp.php?thread_id=' . $_POST['thread_id']);
    exit();
  }


  public function outputCsv($thread_id)
  {
    try {
      $threadModel = new \Bbs\Model\Thread();
      $data = $threadModel->getCommentCsv($thread_id);


      // CSVの出力部分（ファイルに対しての設定部分）
      $csv = array('num', 'username', 'content', 'date');


      // 「mb_convert_encoding」　文字コードの変換（エンコード）
      // エクセルは「ShiftJIS」という文字コードで設定されている。
      // 「UTF-8」　⇒　「SJIS-WIN」
      $csv = mb_convert_encoding($csv, 'SJIS-WIN', 'UTF-8');

      // 「date関数」 ⇒　現在日時の保存。出力するファイル名に使用する。
      $date = date("YmdH:i:s");

      // 『header関数』どういった名前でどういった形式のファイルを出力するのか
      header('Content-Type: application/octet-stream');
      // 「filename=」　⇒　実際のファイル名の部分。
      header('Content-Disposition: attachment; filename=' . $date . '_thread.csv');





      // 今から出力させるファイルにプログラムで内容（データ）を書き込む部分

      // 『ファイルポインター』　⇒　データを記載している位置を表すもの。

      // 「fopen関数」　⇒　ファイルまたはURLをオープンしてくれる関数。
      $stream = fopen('php://output', 'w');
      stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932');
      $i = 0;

      // 繰り返し処理　$data⇒Modelから取得してきたスレッドの情報
      // 1行づつループ処理。
      foreach ($data as $row) {
        //1回目のループ(1行目)
        if ($i === 0) {
          // 「fputcsv関数」⇒CSVの形式でデータを書き込むメソッド。
          //  $csv = array('num', 'username', 'content', 'date')　⇒　配列が保存されている。
          fputcsv($stream, $csv);
        }
        // データベースから取得してきたデータ（1行分）を書き込む。
        fputcsv($stream, $row);
        $i++;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }


  public function searchThread()
  {
    try {
      $this->validateSearch();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('keyword', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrors('keyword', $e->getMessage());
    }

    $keyword = $_GET['keyword'];
    $this->setValues('keyword', $keyword);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadData = $threadModel->searchThread($keyword);
      return $threadData;
    }
  }


  private function validateSearch()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
      if ($_GET['keyword'] === '') {
        throw new \Bbs\Exception\EmptyPost("検索キーワードが入力されていません！");
      }
      if (mb_strlen($_GET['keyword']) > 20) {
        throw new \Bbs\Exception\CharLength("キーワードが長すぎます！");
      }
    }
  }


  // バリデーション～入力値チェック～
  private function validate()
  {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }
    if ($_POST['type'] === 'createthread') {
      if (!isset($_POST['thread_name']) || !isset($_POST['comment'])) {
        echo '不正な投稿です';
        exit();
      }
      if ($_POST['thread_name'] === '' || $_POST['comment'] === '') {
        throw new \Bbs\Exception\EmptyPost("スレッド名または最初のコメントが入力されていません！");
      }


      // 「mb_strlen()」　phpで用意されている関数。
      // （）のなかの文字数を調べて、文字『数』で結果が返ってくる。
      if (mb_strlen($_POST['thread_name']) > 20) {
        throw new \Bbs\Exception\CharLength("スレッド名が長すぎます！");
      }
      if (mb_strlen($_POST['comment']) > 200) {
        throw new \Bbs\Exception\CharLength("コメントが長すぎます！");
      }
    } elseif ($_POST['type'] === 'createcomment') {
      if (!isset($_POST['content'])) {
        echo '不正な投稿です';
        exit();
      }
      if (mb_strlen($_POST['content']) > 200) {
        throw new \Bbs\Exception\CharLength("コメントが長すぎます！");
      }
      if ($_POST['content'] === '') {
        throw new \Bbs\Exception\EmptyPost("コメントが入力されていません！");
      }
    }
  }
}
