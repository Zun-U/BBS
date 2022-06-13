<?php
require_once(__DIR__ .'/../config/config.php');

try {
  $dbh = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
  $stmt = $dbh->query('SELECT * FROM test');
  $stmt->execute();
  $dbh = null;
  $rec = $stmt->fetch(PDO::FETCH_ASSOC);
  echo $rec["name"];
} catch (\PDOException $e) {
  echo $e->getMessage();
  exit;
}
