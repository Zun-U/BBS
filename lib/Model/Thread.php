<?php
namespace Bbs\Model;
class Thread extends \Bbs\Model {
  public function createThread($values) {
    try {
      $this->db->beginTransaction();
      $sql = "INSERT INTO threads (user_id,title,created,modified) VALUES (:user_id,:title,now(),now())";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('user_id',$values['user_id']);
      $stmt->bindValue('title',$values['title']);
      $res = $stmt->execute();
      $thread_id = $this->db->lastInsertId();
      $sql = "INSERT INTO comments (thread_id,comment_num,user_id,content,created,modified) VALUES (:thread_id,1,:user_id,:content,now(),now())";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('thread_id',$thread_id);
      $stmt->bindValue('user_id',$values['user_id']);
      $stmt->bindValue('content',$values['comment']);
      $res = $stmt->execute();
      $this->db->commit();
    } catch (\Exception $e) {
      echo $e->getMessage();
      $this->db->rollBack();
    }
  }

}