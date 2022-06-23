<?php
namespace Bbs\Exception;
class DeleteUser extends \Exception {
  protected $message = 'すでに退会済みのユーザーです。';
}