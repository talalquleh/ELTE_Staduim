<?php 

class CommentStorage extends Storage {
    public function __construct() {
      parent::__construct(new JsonIO('../src/storage/comments.json'));
    }
  }


?>