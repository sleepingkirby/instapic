<?php
require('./includes/urlParse.php');
require('./includes/auth.php');
require('./includes/posts.php');
require('./config/mariadb_cnf.php');
require('./config/auth_cnf.php');
require('./config/posts_cnf.php');



/*
data in:
username
password
user token
data model:
  {
  title: "title",
  descrip: "asdfasdf",
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

//there are no url objects, (i.e. path is like /), do nothing.
if(count($url->urlObj)<=0 || $url->urlObj[0]=="" ||$url->urlObj[0]==null||$url->urlObj[0]==false){
return null;
}


$post=array('username'=>"", 'password'=>"", 'active'=>"", 'timeout'=>0);
  if(array_key_exists('json',$_POST)){
  $post=array_merge($post, json_decode($_POST['json'], true));//makes default keys exist
  }
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
        return $auth->logout($url->urlObj[1], $url->tkn);
        }
        elseif(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="loggedIn"&&$url['act']=="PATCH"){
        return $auth->loggedIn($url->urlObj[1], $url->tkn);//this function keeps the session alive. Why have it available? For the option to do no action, but keep the session alive.
        }
      break;
    }
  break;
  case "posts":
/*
  if user is logged in, they can
  a) upload (/posts/post, POST), modify posts(/posts/post/<id>, PUT)
  b) get a list of all posts (/posts/list , GET)
*/
    $userInfo=$auth->loggedIn($url->user, $url->tkn);
    //if the session is not valid, go no further.
    if(!$userInfo['status']){
    return $userInfo;
    }

    $posts=new posts($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_posts['fileLoc']);
    if(count($url->urlObj)==2){
      switch($url->urlObj[1]){
        case "post":
        //clear;curl -F 'json={"title":"testfile", "descr":"testfile description", "tags":"test,test2"}' -F "file=@/home/sleepingkirby/tmp.txt" -H "Authorization: testtkn" -H "Username: testUser" http://sleepingkirby.local/posts/post
          if(!is_array($_POST)||!array_key_exists('json',$_POST)){
            $rtrn['status']=false;
            $rtrn['msg']="Data model for picture missing.";
            return $rtrn;
          }
          $json="";
          if(array_key_exists('json',$_POST)){
            $json=json_decode($_POST['json'], true);
          }
          if(json_last_error()!=JSON_ERROR_NONE){
            $rtrn['status']=false;
            $rtrn['msg']=json_last_error_msg();
            return $rtrn;
          }

        return $posts->upload($url->user,$_FILES["file"]["tmp_name"],$_FILES["file"]["name"],$json['title'],$json['descr'],$json['tags']);
        break;
        case "list":
        //clear;curl -F 'json={"username":"testUser", "sort":"datetime"}' -H "Authorization: testtkn" -H "Username: testUser" http://sleepingkirby.local/posts/list
          $json="";
          if(array_key_exists('json',$_POST)){
            $json=json_decode($_POST['json'], true);
          }
          if(json_last_error()!=JSON_ERROR_NONE){
            $rtrn['status']=false;
            $rtrn['msg']=json_last_error_msg();
            return $rtrn;
          }
        $val=$posts->get($json);
        var_dump($val);
        return $posts->get($json);
        break;
        default:
        break;
      }
    }
  break;
  default:
  break;
}

//data retriever





?>
