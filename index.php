<?php
require('./includes/mariadb.php');
require('./includes/urlParse.php');
require('./includes/auth.php');
require('./includes/posts.php');
require('./config/mariadb_cnf.php');
require('./config/auth_cnf.php');
require('./config/posts_cnf.php');


//query parser
$url = new urlParse();
$url->main(); //parse url

//auth module
$auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen'], $_auth['status']);

//there are no url objects, (i.e. path is like /), do nothing.
if(count($url->urlObj)<=0 || $url->urlObj[0]=="" ||$url->urlObj[0]==null||$url->urlObj[0]==false){
return null;
}

$post=array('password'=>"", 'active'=>"", 'timeout'=>0);
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
      //curl -v -F 'json={"username":"testUser2", "password":"password", "status":"active", "timeout":"50"}' -X POST http://sleepingkirby.local/users/register
        if($url->act=="POST"){
          echo json_encode($auth->register($post['username'],$post['password'], $post['status'], $post['timeout']));
          return null;
        }
      break;
      default:
        //attempt to login
        //curl -F 'json={"password":"password"}' -X POST http://domain/users/username/login
        if(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="login"&&$url->act=="POST"){
        echo json_encode($auth->login($url->urlObj[1], $post['password']));
        return null;
        }
        elseif(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="logout"&&$url->act=="PATCH"){
        //clear;curl -v -H "Authorization: 01102033beb353adefa963e71dac3ae83b5e17fe3808ae93af" -H "Username: testUser" -X PATCH http://sleepingkirby.local/users/testUser/logout
        echo json_encode($auth->logout($url->urlObj[1], $url->tkn));
        return null;
        }
        elseif(is_string($url->urlObj[1])&&array_key_exists(2,$url->urlObj)&&$url->urlObj[2]=="loggedIn"&&$url->act=="PATCH"){
        echo json_encode($auth->loggedIn($url->urlObj[1], $url->tkn));//this function keeps the session alive. Why have it available? For the option to do no action, but keep the session alive.
        return null;
        }
      break;
    }
  break;
  case "posts":
    $posts=new posts($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_posts['fileLoc']);
    if(count($url->urlObj)==2){
      switch($url->urlObj[1]){
        case "post":
        $userInfo=$auth->loggedIn($url->user, $url->tkn);
          //if the session is not valid, go no further.
          if(!$userInfo['status']){
          echo json_encode($userInfo);
          return null;
          }

        //clear;curl -F 'json={"title":"testfile", "descr":"testfile description", "tags":"test,test2"}' -F "file=@/home/sleepingkirby/Pictures/EalqtfJXYAswGVP.png" -H "Authorization: testtkn" -H "Username: testUser" http://sleepingkirby.local/posts/post
          if(!is_array($_POST)||!array_key_exists('json',$_POST)){
            $rtrn['status']=false;
            $rtrn['msg']="Data model for picture missing.";
            echo json_encode($rtrn);
            return null;
          }
          $json="";
          if(array_key_exists('json',$_POST)){
            $json=json_decode($_POST['json'], true);
          }
          if(json_last_error()!=JSON_ERROR_NONE){
            $rtrn['status']=false;
            $rtrn['msg']=json_last_error_msg();
            echo json_encode($rtrn);
            return null;
          }

        echo json_encode($posts->upload($url->user,$_FILES["file"]["tmp_name"],$_FILES["file"]["name"],$json['title'],$json['descr'],$json['tags']));
        return null;
        break;
        case "list":
        $userInfo=$auth->loggedIn($url->user, $url->tkn);
          //if the session is not valid, go no further.
          if(!$userInfo['status']){
          echo json_encode($userInfo);
          return null;
          }

        //clear;curl -F 'json={"username":"testUser", "sort":"datetime"}' http://sleepingkirby.local/posts/list
          $json="";
          if(array_key_exists('json',$_POST)){
            $json=json_decode($_POST['json'], true);
          }
          if(json_last_error()!=JSON_ERROR_NONE){
            $rtrn['status']=false;
            $rtrn['msg']=json_last_error_msg();
            echo json_encode($rtrn);
            return null;
          }
        echo json_encode($posts->get($json));
        return null;
        break;
        default:

        //clear;curl http://sleepingkirby.local/posts/3 --output ./tmp.png
          if(is_numeric($url->urlObj[1])){
          $posts->getPic($url->urlObj[1]);
          }
        return null;
        break;
      }
    }
  break;
  default:
  break;
}

//data retriever





?>
