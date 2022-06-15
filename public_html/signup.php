 <?php

    // require_once　「/header.php」を読み込んでいる。
    // ヘッダーの部分はよく使うので、使い回せるように１つのファイルに内容をまとめている。（ヘッダーの部分を分割している。）
    // require_once() →　()の中に書かれているファイルを読み込みますというPHPの関数
    // __DIR__　→　PHPに用意されているマジカル変数。今回は、このheader.phpが保存しているファイルの『パス』を読み込む。その中の/header.phpまでのパスまで辿っている。
    // 「読み込む」というのはsignup.phpにheader.phpに書かれている内容を「付け足す」イメージ。
    require_once(__DIR__ . '/header.php');


    // __DIR__　⇒　なにか知りたければ、var_dump()を使う。
    // 今回は「string(31) "C:\xampp\htdocs\bbs\public_html"」とpublic_htmlまでのパスが表示される。
    // 「__DIR__」『現在自分がいるパスを取得する』というPHPで予め用意された関数。
    // var_dump(__DIR__);

    // Signupクラスのインスタンス化
    // Controllerフォルダ内のSignup.phpの「Signup」クラスに書かれている処理を使えるようにしている。
    $app = new Bbs\Controller\Signup();


    // Signupクラスの「run」という関数を実行してくださいの意味
    $app->run();
    ?>
    <div class="container">

      <!--<form>タグのmethod属性が「post」  -->
      <!-- action属性が「””」と空である場合は、自分自身にフォーム送信（再読み込み）される。 -->
      <form action="" method="post" id="signup" class="form">



        <div class="form-group">
          <label>メールアドレス</label>

          <!--$app->getValues()->email  -->
          <!--  ControllerクラスのgetValuesメソッドを実行している。このメソッドの目的としては、入力エラーで送信できなくても、フォームの入力値は消えないように値を『保存（保持）』する。 -->
          <!-- ☆☆　SignupクラスでControllerクラス（親クラス）を「extends（継承）」しているから、親クラスのgetValuesメソッドを自分の物のように使える。（Signupクラスをインスタンス化したため）　☆☆ -->

          <!-- ☆☆☆　三項演算子（１行で「IF文」のようなことが出来る。）　☆☆☆ -->

          <!-- 「isset」
          入力された情報があったのかなかったのか、データーベースから情報が取得されているのかされていないのか。されていない場合はfalseという結果が帰ってくる。trueかfalseなのかで判定される。 -->

          <!-- isset($app->getValues()->email)　⇒　条件の部分（if()のカッコの部分）。
          今回はフォームの入力欄に何か記入されていたかどうかを条件としている。
          記入されていたら「getValues」で入力された値を取得してくる。 -->

          <!-- 「？」の後に書かれているものは、条件が”正しかった”場合。 -->
          <!-- h($app->getValues()->email) : ''; -->
          <!-- コロンの手前は条件が「true（真）」のときだった場合の処理、コロンの後半は条件が「false（偽）」の場合だったときの処理 -->

          <!-- この場合は、エラーが起きた時にフォームになにか入力されている場合はその入力した値を「残します」よ、入力がない場合はフォーム欄を「空欄」にしますよという処理になる。 -->
          <input type="text" name="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>" class="form-control">


          <!-- $app->getErrors('email')　⇒　エラーメッセージを取得して表示。 -->
          <p class="err"><?= h($app->getErrors('email')); ?></p>


        </div>
        <div class="form-group">
          <label>ユーザー名</label>
          <input type="text" name="username" value="<?= isset($app->getValues()->username) ? h($app->getValues()->username) : ''; ?>" class="form-control">
          <p class="err"><?= h($app->getErrors('username')); ?></p>
        </div>
        <div class="form-group">
          <label>パスワード</label>
          <input type="password" name="password" class="form-control">
          <p class="err"><?= h($app->getErrors('password')); ?></p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('signup').submit();">登録</button>


        <!-- フォーム送信をする時に、tokenを送信する。 -->
        <!-- どのサイトからやてきたかの『通行証』のようなもの。セキュリティ対策のため。 -->
        <!-- 「SESSION変数」　ユーザーがログインしてからログアウトするまでに必要な値をセッション変数として保存して
        「保持」できる -->
        <!-- 画面に表示されないフォーム部品を使って「token」を付与している。 -->
        <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">



      </form>
      <p class="fs12"><a href="<?= SITE_URL; ?>/login.php">ログイン</a></p>
    </div><!-- container -->
    <?php require_once(__DIR__ . '/footer.php'); ?>