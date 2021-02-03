<?php
require('./includes/mariadb.php');

class auth extends sqlClass{

  public function __construct($u, $p, $db, $h){
    parent::__construct($u, $p, $db, $h);
  }

  public function __destruct(){
    parent::__destruct();
  }

}


?>
