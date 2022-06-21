<?php
require_once(__DIR__ . '/header.php');

// ModelのThreadクラスをインスタンス化
$threadMod = new Bbs\Model\Thread();


// Threadクラスの「getThreadAll」処理を実行。
// 「$threads」⇒　「Threadクラス」にある関数名「getThreadAll()」の処理をしまっている。
// つまり、SQL文で取得してきた結果を、「全件（複数行）」取得したものが格納されている。
$threads = $threadMod->getThreadAll();

?>
<h1 class="page__ttl">スレッド一覧</h1>
<ul class="thread">

  <!-- foreach文 -->
  <!-- foreach ('配列' as '配列に保存された値を一つ一つ取り出して保存する為の変数') -->

  <!-- <ul>タグの中に「foreach」がある。ここでは、「スレッドの数だけ<li>タグを増やす。」という仕組みになっている。 -->
  <!-- 配列に保存された値の数だけ反復処理を行うと言う特徴から『配列に対して』使用する。 -->
  <!-- ここでは、「$threads」（SQL文で取得してきた全件結果）を「$thread」（配列に保存された値を一つ一つ取り出して保存する為の器）に値が無くなるまで、一つづつ保存される -->
  <!--　$threadには繰り返しの回数に応じて、配列の1個1個の値が挿入される。  -->
  <?php foreach ($threads as $thread) : ?>


    <li class="thread__item" data-threadid="<?= $thread->t_id; ?>">
      <div class="thread__head">
        <h2 class="thread__ttl">

          <!--$thread  -->

          <!-- オブジェクトも連想配列と同じようにkeyと値の組み合わせでデータを保持することが出来る。 -->
          <!-- ただし、オブジェクトと連想配列ではkeyと値の『扱い方が異なる』。 -->
          <!-- 連想配列の値の取り出し方の場合　⇒ $thread["title"]-->

          <!-- ☆☆　オブジェクトの場合、keyに値する部分を「プロパティ」と呼ぶ。 ☆☆-->
          <!-- 『オブジェクト』の中に変数や関数を書いていく。 -->
          <!-- クラスの中の変数のことを『プロパティ』と呼ぶ。 -->


          <!-- クラスをインスタンス化(実体化)させた後に、 -->
          <!-- $objectTest->like -->
          <!-- と 矢印(アロー演算子) からの 変数名(プロパティ) を指定することで、呼び出すことができる。 -->
          <?= h($thread->title); ?>


          <!-- ★オブジェクトのメリット★ -->
          <!-- オブジェクトにするメリットとしては、開発の規模が大きくなった際にプロパティと一緒に処理も設定できること。規模が大きくなるにつれて、管理するデータも増える。しかしオブジェクトであれば、関数はクラスに紐づいているため、対象のクラスから容易にアクセスや修正が行うことができる。 -->


        </h2>
        <div class="fav__btn<?php if(isset($thread->f_id)) { echo ' active';} ?>"><i class="fas fa-star"></i></div>
      </div>
      <ul class="thread__body">
        <?php
        $comments = $threadMod->getComment($thread->t_id);
        foreach ($comments as $comment) :
        ?>
          <li class="comment__item">
            <div class="comment__item__head">
              <span class="commit__item__num"><?= h($comment->commnt_num); ?></span>
              <span class="comment__item__name">名前:<?= h($comment->username); ?></span>
              <span class="comment__item__date">投稿日時:<?= h($commit->created)($comment->created); ?></span>
            </div>
            <p class="comment__item__content"><?= h($comment->content); ?></p>
          <?php endforeach; ?>

          </li>
      </ul>
      <div class="operation">

      <!--/Model/Thread.phpの「getCommentCount」という関数に、($thread->id)とスレッドのIDを渡してあげている。（スレッドIDが分かれば、どのスレッドか特定できるため。） -->


      <!-- クエリパラメーター -->
      <!-- ?thread_id=.....の部分 -->
      <!-- スレッドIDはどのスレッドか特定できる値になる。 -->
        <a href="<?= SITE_URL; ?>/thread_disp.php?thread_id=<?= $thread->t_id; ?>">書き込む&すべて読む(<?= h($threadMod->getCommentCount($thread->t_id)); ?>)</a>
        <p class="thread__date">スレッド作成日時:<?= h($thread->created); ?></p>
      </div>
    </li>
  <?php endforeach; ?>
</ul><!-- thread -->


<?php
require_once(__DIR__  . '/footer.php');
?>