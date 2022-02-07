<?php 

class TeamStorage extends Storage {
    public function __construct() {
      parent::__construct(new JsonIO('../src/storage/teams.json'));
    }
  }


?>