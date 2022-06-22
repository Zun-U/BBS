<?php
require_once(__DIR__.'/header.php');
$threadMod = new Bbs\Model\Thread();
$threads = $threadMod->getThreadFavoriteAll();
?>
<h1 class="page__ttl">お気に入り一覧</h1>
<ul class="thread">
  <?php foreach($threads as $thread): ?>



    <!-- カスタムデータ属性 -->
    <!-- 属性の名前と属性の値を自由に決めることが出来る。カスタムデータ属性は主にJavaScriptで値を取得するときに使われる。 -->
    <!-- 『data-』　で定義できる。（data- ⇒　プリフィックス、接頭語。カスタム属性には必ずこれが必要。） -->
    <!-- 動的に値を設定できる。（JSでDOM属性の値を取得できるようになるため。） -->
    <li class="thread__item" data-threadid="<?= $thread->t_id; ?>">

    <!-- ☆☆☆　class属性は『スタイル情報を割り当てること』が目的で、"データを格納する"には適切では『無い』　☆☆☆ -->



    <div class="thread__head">
      <h2 class="thread__ttl">
        <?= h($thread->title); ?>
      </h2>

      <!--  「echo ' active';」 の「active」は属性-->
      <!-- ☆☆　タグには複数のクラスを設定できる。☆☆ -->
      <!-- お気に入りされている状態だと、「<div class="fav__btn　active"...」と2つのクラスが付与される。 -->
      <div class="fav__btn<?php if(isset($thread->f_id)){ echo ' active';} ?>"><i class="fas fa-star"></i></div>
    </div>
    <ul class="thread__body">
      <?php $comments = $threadMod->getComment($thread->t_id);
      foreach($comments as $comment):
      ?>
      <li class="comment__item">
      <div class="comment__item__head">
        <span class="comment__item__num"><?= h($comment->comment_num); ?></span>
        <span class="comment__item__name">名前:<?= h($comment->username);?></span>
        <span class="comment__item__date">投稿日時:<?= h($comment->created);?></span>
      </div>
      <p class="comment__item__content"><?= h($comment->content); ?></p>
      <?php endforeach; ?>
      </li>
    </ul>
    <div class="operation">
      <a href="<?= SITE_URL; ?>/thread_disp.php?thread_id=<?= $thread->t_id; ?>">書き込み&すべて読む(<?= h($threadMod->getCommentCount($thread->t_id)); ?>)</a>
      <p class="thread__date">スレッド作成日時:<?= h($thread->created); ?></p>
    </div>
  </li>
  <?php endforeach?>
</ul><!-- thread -->
<?php
require_once(__DIR__ .'/footer.php');
?>