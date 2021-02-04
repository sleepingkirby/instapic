<?php
require('./includes/urlParse.php');
require('./includes/auth.php');
require('./includes/common.php');
require('./config/mariadb_cnf.php');
require('./config/auth_cnf.php');



/*
data in:
username
password
user token
data model:
  {
  title: "title",
  descrip: "asdfasdf",
  w: num,
  h: num,
  tag: "comma delineated words"
  }
picture

url query
*/
// clear;curl -F 'json={"key1":"value1", "key2":"value2"}' -F "file=@/home/sleepingkirby/tmp.txt" http://sleepingkirby.local/
//curl -d '{"key1":"value1", "key2":"value2"}' -H "Content-Type: application/json" -X POST http://localhost:3000/data
//curl -i -H "Accept: application/json" -H "Content-Type: application/json" http://hostname/resource
//curl -X POST -d @filename http://hostname/resource



//query parser
$url = new urlParse();
$url->main(); //parse url

//auth module
$auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);
/*
var_dump($auth->register('testUser','testPass', 'active', 50));
$status=$auth->login('testUser', 'testPass');
var_dump($auth->loggedIn("testUser", $status['tkn']));
*/

//there are no url objects, (i.e. path is like /), do nothing.
if(count($url->urlObj)<=0 || $url->urlObj[0]=="" ||$url->urlObj[0]==null||$url->urlObj[0]==false){
return null;
}

/*
  if user is logged in, they can
  a) upload (/posts/post, POST), modify posts(/posts/post/<id>, PUT)
  b) get a list of all posts (/posts/list , GET)
*/
$post=array('username'=>"", 'password'=>"", 'token'=>"", 'active'=>"", 'timeout'=>0);
$post=array_merge($post, json_decode($_POST['json'], true));//makes default keys exist
switch($url->urlObj[0]){
  case "users":
    // only 1 object
    if(count($url->urlObj)==1){
    //documentation
    return null;
    } 
    switch($url->urlObj[1]){
      case "register":
        if($url['act']=="POST"){
          return $auth->register($post['username'],$post['password'], $post['status'], $post['timeout']);
        }
      break;
      default:
        //attempt to login
        if(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="login"&&$url['act']=="POST"){
        return $auth->login($url->urlObj[1], $post['password']);
        }
        elseif(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="logout"&&$url['act']=="PATCH"){
        return $auth->logout($url->urlObj[1], $post['token']);
        }
        elseif(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="loggedIn"&&$url['act']=="PATCH"){
        return $auth->loggedIn($url->urlObj[1], $post['token']);
        }
      break;
    }
  break;
  case "posts":
    
  break;
  default:
  break;
}

//data retriever





?>
