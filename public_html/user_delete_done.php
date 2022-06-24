<!-- ここは『UserDelete』クラスに処理を渡すためだけの部分。 -->

<?php
require_once(__DIR__.'/header.php');
$app = new Bbs\Controller\UserDelete();
$app->run();
