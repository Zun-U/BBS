<?php
require_once(__DIR__ . '/header.php');
$app = new Bbs\Controller\UserUpdate();
$app->run();
?>

<h1 class="page__ttl">マイページ</h1>
<div class="container">


  <!--「enctype」 そのフォームの送信内容に画像（またはファイル）を含む場合に、enctypeを記述しなければいけない。 -->
  <!-- ※送信データのエンコード・タイプを指定する属性。form要素のmethod属性がpostのときだけ使う。 -->
  <form action="" method="post" id="userupdate" class="form mypage-form row" enctype="multipart/form-data">



    <div class="col-md-8">
      <div class="form-group">
        <label>メールアドレス</label>

        <!-- 「getValues()->email」⇒　データベースから取得してきたemail -->
        <input type="text" name="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>" class="form-control">
        <p class="err"><?= h($app->getErrors('email')); ?></p>
      </div>
      <div class="form-group">
        <label>ユーザー名</label>
        <input type="text" name="username" value="<?= isset($app->getValues()->username) ? h($app->getValues()->username) : ''; ?>" class="form-control">
        <p class="err"><?= h($app->getErrors('username')); ?></p>
      </div>
      <button class="btn btn-primary" onclick="document.getElementById('userupdate').submit();">更新</button>
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">


      <!-- 以前設定されていた「画像の情報」を持つフォームの部品 -->
      <input type="hidden" name="old_image" value="<?= h($app->getValues()->image); ?>">



      <p class="err"></p>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <!-- <p class="err"></p> -->
        <div class="imgarea <?= isset($app->getValues()->image) ? '' : 'noimage' ?>">
          <label>
            <span class="file-btn">
              編集

              <!-- 「type="file"」⇒ -->
              <input type="file" name="image" class="form" style="display:none" accept="image/*">


            </span>
          </label>
          <div class="imgfile">

          <!-- 画像を表示する部分 -->
          <!-- './gazou/'　⇒　画像フォルダの -->
          <!-- h($app->getValues()->image)　⇒　ユーザーが設定した画像 -->
            <img src="<?= isset($app->getValues()->image) ? './gazou/' . h($app->getValues()->image) : './asset/img/noimage.png'; ?>" alt="">
          </div>
        </div>
      </div>
    </div>
  </form>
</div><!-- container -->
<?php
require_once(__DIR__ . '/footer.php');
?>