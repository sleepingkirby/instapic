<?php declare(strict_types=1);
require('./includes/mariadb.php');
require("./includes/auth.php");


use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    public function testLogin(): void
    {
      require("./config/auth_cnf.php");
      require("./config/mariadb_cnf.php");
      $auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);
      $out=$auth->register('autotestuser','autotestpass','active',50);
      $out=$auth->login('autotestuser','autotestpass');
      $this->assertTrue($out['status']);//true
      $out=$auth->login('badusername','badpassword');
      $this->assertFalse($out['status'],$out['msg']);//bad username
      $out=$auth->login('autotestuser','badpassword');
      $this->assertFalse($out['status'],$out['msg']);//bad password
    }

    public function testLoggedIn(): void
    {
      require("./config/auth_cnf.php");
      require("./config/mariadb_cnf.php");
      $auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);
      $userInfo=$auth->login('autotestuser','autotestpass');
      $out=$auth->loggedIn('autotestuser',$userInfo['tkn']);
      $this->assertTrue($out['status']);//true 
      $out=$auth->loggedIn('autotestuser','badtoken');
      $this->assertFalse($out['status'],$out['msg']);//bad token
    }

    public function testLogOut(): void
    {
      require("./config/auth_cnf.php");
      require("./config/mariadb_cnf.php");
      $auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);
      $userInfo=$auth->login('autotestuser','autotestpass');
      $out=$auth->logout('',$userInfo['tkn']);
      $this->assertFalse($out['status'],$out['msg']);//no username
      $out=$auth->logout('asdfasdfasdfasdfasdfadsfasdfese','badtoken');
      $this->assertFalse($out['status'],$out['msg']);//bad username
      $out=$auth->logout('autotestuser',$userInfo['tkn']);
      $this->assertTrue($out['status']);//true
    }


}
?>
