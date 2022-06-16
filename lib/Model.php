<?php
namespace Bbs;
class Model {

  //「protected」というアクセス修飾子で「$db」という変数が定義されている。
  // 自身のクラスと、自分（Modelクラス）を継承した子クラスにのみ、変数「$db」のアクセス権を付与している。
  protected $db;


  public function __construct(){
    // Modelクラス及び子クラスのインスタンスを生成した際には、必ずPDOクラスのインスタンスを生成する
    try {


      // 「PDO」　PHPで予め用意されている『データベースに接続する』ための関数。
      // ()内は、データベースに『接続するのに必要な情報を書く』。今回はconfig.phpでそれぞれの定数にパスワード等が保存されている。「DSN」, 「DB_USERNAME」, 「DB_PASSWORD」は定数になり、何かしらの値が保存されている。
      // 「new \PDO(......)」データベース(DB)に色々操作を行う関数がつまったクラスをインスタンス化している。
      $this->db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);


    // データベース接続にエラーが発生した場合（データベースへ接続する為のユーザー情報が間違っていた等）、例外処理としてcatchの処理が実行される。
    // ここではエラーのメッセージが表示される。
    // 「\PDOException」⇒ PHP側で予め用意された『DB接続に関する例外処理』がまとめられたクラス。
    } catch (\PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }
}
