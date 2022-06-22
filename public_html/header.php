<?php
require_once(__DIR__ . '/../config/config.php');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>codelab掲示板</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link href="https://fonts.googleapis.com/css?family=Charm|M+PLUS+Rounded+1c&amp;subset=latin-ext,thai,vietnamese" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/8bc1904d08.js"></script>
  <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
  <header class="sticky-top header">
    <div class="header__inner">
      <nav>
        <ul>
          <li><a href="<?= SITE_URL; ?>/">ホーム</a></li>
          <?php

          // 「isset()」⇒ ()の中に値が保存されていれば「true」。「true」であれば、ここではif文の中が実行される。
          if (isset($_SESSION['me'])) { ?>
            <li><a href="<?= SITE_URL; ?>/thread_all.php">一覧</a></li>
            <li><a href="<?= SITE_URL; ?>/thread_favorite.php">お気に入り</a></li>
            <li><a href="<?= SITE_URL; ?>/thread_create.php">作成</a></li>
          <?php }

          // 値が保存されていなければ、else{}が実行される。
          else { ?>
            <li class="user-btn"><a href="<?= SITE_URL; ?>/login.php">ログイン</a></li>
            <li><a href="<?= SITE_URL; ?>/signup.php">ユーザー登録</a></li>
          <?php } ?>
        </ul>
      </nav>
      <div class="header-r">
        <?php
        // ↓↓セッション変数があれば、というif文。つまりログインしてるか、していないかをSESSION変数の有無で判断している。
        if (isset($_SESSION['me'])) { ?>


          <!-- カスタムデータ属性 -->
          <!--  ここでは「data-me」というデータ属性にユーザーIDの値が定義されている。 -->
          <div class="prof-show" data-me="<?= h($_SESSION['me']->id); ?>">





            <!-- h($_SESSION['me']->username) ⇒　 $_SESSION['me']のkeyに紐づくusername、という意味-->
            <!-- 「＜?=」は「＜?php echo」の省略形  -->
            <a href="<?= SITE_URL; ?>/mypage.php"><span class="name"><?= h($_SESSION['me']->username); ?></span>


              <!--  「div」は使用すると改行が入る為、レイアウトが崩れてしまう -->
              <!--  「span」は文の”中”でのくくり -->
              <span class="image">


                <!-- ログインしたユーザーのセッション情報にアップロードした画像データが存在（isset）存在した時-->
                <!-- UserUpdate．phpで定義されている　⇒　「$_SESSION['me']->image = $user_img['name']」 -->
                <?php if (isset($_SESSION['me']->image)) : ?>


                  <img src="<?= SITE_URL; ?>/gazou/<?= h($_SESSION['me']->image); ?>" alt="">
                <?php else : ?>
                  <img src="<?= SITE_URL; ?>/asset/img/noimage.png" alt="">
                <?php endif; ?>
              </span>



            </a>
          </div>
          <form action="logout.php" method="post" id="logout" class="user-btn">
            <input type="submit" value="ログアウト">
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
          </form>
        <?php  } ?>
      </div>
    </div>
  </header>
  <div class="wrapper">