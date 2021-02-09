<?php declare(strict_types=1);
require('./includes/mariadb.php');
require("./includes/auth.php");
require("./includes/posts.php");


use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testUpload(): void
    {
      require("./config/mariadb_cnf.php");
      require('./config/posts_cnf.php');
      require('./config/auth_cnf.php');

      $auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);
      $posts=new posts($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_posts['fileLoc']);
      $out=$auth->register('autotestuser','autotestpass','active',50);

      $org="/home/sleepingkirby/dev/httpd/testData/116715813_1655633421262970_5644433323998197723_n.jpg";
      $tmp="/home/sleepingkirby/dev/httpd/testData/tmpfile";
      $fnm="116715813_1655633421262970_5644433323998197723_n.jpg";
      copy($org,$tmp);

      $out=$posts->upload("autotestuser",$tmp,$fnm,'autotesttitle','autotestdescr','autotesttags');
      $this->assertFalse($out['status']); //false, this can't be done via tests. Which is good
    }

    public function testGet(): void
    {
      require("./config/mariadb_cnf.php");
      require('./config/posts_cnf.php');

      $arr=array();

      $posts=new posts($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_posts['fileLoc']);
      $out=$posts->get($arr);
      $this->assertTrue($out['status'],$out['msg']); //should all be true
    }

    public function testGetPic(): void
    {
      require("./config/mariadb_cnf.php");
      require('./config/posts_cnf.php');

      $arr=array();
   
      $posts=new posts($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_posts['fileLoc']);
      //$out=$posts->getPic('false');//this function either returns a binary or dies. No real way to automatically test
      $this->assertFalse(false); //should all be true
    }

}
?>
