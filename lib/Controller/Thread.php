<?php

namespace Bbs\Controller;

class Thread extends \Bbs\Controller
{
  public function run()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


      // $_POST['type']　⇒　「type」というname属性を持っているフォーム部品からPOST送信された値が、
      // 「createthread」という値と完全に一致すれば、という条件分岐。

      // つまり、「スレッド作成」ボタンを押したら、createThread（スレッド作成処理の関数名）を実行しなさい、の意。
      if ($_POST['type']  === 'createthread') {
        $this->createThread();
      } else if ($_POST['type'] === 'createcomment') {
        $this->createComment();
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
