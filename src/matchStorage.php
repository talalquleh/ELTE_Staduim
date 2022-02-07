<?php 

class MatchStorage extends Storage {
    public function __construct() {
      parent::__construct(new JsonIO('../src/storage/matches.json'));
    }
  }


?>