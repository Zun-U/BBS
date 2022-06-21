<?php
require_once(__DIR__.'/header.php');
$threadMod = new \Bbs\Model\Thread();
$threads = $threadMod->getThreadFavoriteAll();
?>
<h1 class="page__ttl">お気に入り一覧</h1>
<ul class="thread">
  <?php foreach($threads as $thread): ?>
    <li class="thread__item" data-threadid="<?= $thread->t_id; ?>"></li>
</ul>