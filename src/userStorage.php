<?php 

class UserStorage extends Storage {
    public function __construct() {
      parent::__construct(new JsonIO('../src/storage/users.json'));
    }
  }


?>