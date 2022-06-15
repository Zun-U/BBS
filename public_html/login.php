<?php
// ヘッダーの読み込み
require_once(__DIR__ .'/header.php');

// Loginクラスのインスタンス化
// 一つの機能にたいして一つのControllerがあるとベスト。login機能はLoginクラスにまとめられている。
// あるクラスに書かれている関数が使いたい場合は、「必ず先に」インスタンス化すること。
$app = new Bbs\Controller\Login();
$app->run();
?>
<div class="container">
  <form action="" method="post" id="login" class="form">
    <div class="form-group">
      <label>メールアドレス</label>
      <input type="text" name="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>" class="form-control">
    </div>
    <div class="form-group">
      <label>パスワード</label>
      <input type="password" name="password" class="form-control">
    </div>

    <!-- setErrorsで保存した値（エラーメッセージ）の呼び出し -->
    <p class="err"><?= h($app->getErrors('login')); ?></p>
    <button class="btn btn-primary" onclick="document.getElementById('login').submit();">ログイン</button>

    <!-- $_SESSION['token']を隠しながらフォーム送信している。↓↓ -->
    <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
  </form>
  <p class="fs12"><a href="signup.php">ユーザー登録</a></p>
</div><!--container -->
<?php require_once(__DIR__ .'/footer.php'); ?>