<?php
namespace Bbs;
class Model {
  protected $db;
  public function __construct(){
    // Modelクラス及び子クラスのインスタンスを生成した際には、必ずPDOクラスのインスタンスを生成する
    try {


      // 「PDO」　PHPで予め用意されている『データベースに接続する』ための関数。
      // ()内は、データベースに接続するのに必要な情報を書く。今回はconfig.phpでそれぞれの定数にパスワード等が保存されている。
      $this->db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
    } catch (\PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }
}
