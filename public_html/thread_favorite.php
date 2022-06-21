<?php
require_once(__DIR__.'/header.php');
$threadMod = new \Bbs\Model\Thread();
$threads = $threadMod->getThreadFavoriteAll();
?>
<h1 class="page__ttl">お気に入り一覧</h1>
<ul class="thread">
  <?php foreach($threads as $thread): ?>
    <li class="thread__item" data-threadid="<?= $thread->t_id; ?>">
    <div class="thread__head">
      <h2 class="thread__ttl">
        <?= h($thread->title); ?>
      </h2>
      <div class="fav__btn<?php if(isset($thread->f_id)){ echo ' active';} ?>"><i class="fas fa-star"></i></div>
    </div>
    <ul class="thread__body">
      <?php $comments = $threadMod->getComment($thread->t_id);
      foreach($comments as $comment);
      ?>
      <li class="comment__item"></li>
      <div class="comment__item__head">
        <span class="comment__item__num"><?= h($comment->comment_num); ?></span>
        <span class=""
      </div>
    </ul>
  </li>
</ul>