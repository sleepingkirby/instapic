<?php

/*
class that parses and handles url requests
*/
class urlParse{
public $resp;
public $act;
public $url;
public $user;
public $tkn;
public $urlObj;

  public function __construct(){
    $this->resp['200']='Ok.';
    $this->resp['204']='Method Not Allowed.';
    $this->resp['401']='Unauthorized. Missing credentials.';
    $this->resp['403']='Invalid credentials.';
    $this->resp['404']='Not Found';
    $this->resp['405']='Method Not Allowed';
    $this->resp['418']='I\'m a teapot.';
    $this->resp['500']='Internal Server Error.';
    $this->url="";
    $this->act="";
    $this->user="";
    $this->tkn="";
    $arr=apache_request_headers();
      if(array_key_exists('Authorization', $arr)){
      $this->tkn=$arr["Authorization"];
      }

      if(array_key_exists('Username', $arr)){
      $this->user=$arr["Username"];
      }
  }

  public function varDump(){
  var_dump($this->act);
  var_dump($this->url);
  var_dump($this->urlObj);
  }

  /*get the objects of the url*/
  public function parseUrl($u=null){
    $this->url=$u;
    if(!$this->url || $this->url==""){
    $this->url=$_SERVER["REQUEST_URI"];
    }

      //strip first / if it exists
      if($this->url[0]=='/'){
        $this->url=substr($this->url, 1, strlen($this->url)-1);
      }
      //strip last / if it exists
      if(strlen($this->url)>=1 && $this->url[strlen($this->url)-1]=='/'){
        $this->url=substr($this->url, 0, strlen($this->url)-1);
      }

    return explode('/',$this->url);
  }


  /* writes headers to respond via http response codes*/
  public function httpResp($i){
    if(!$i || !array_key_exists($i, $this->resp)){ 
      header($_SERVER["SERVER_PROTOCOL"]." 404");
      print($this->resp['404']);
      exit(1);
    }
    header($_SERVER["SERVER_PROTOCOL"]." ".$i);
    if($i>=200 && $i<=299){
    return true;
    }

    print($this->resp[$i]);
    exit(1);
  }

    // the switch that translate actins into functions
    //to be defined by child classes.
    public function do($act, $url){
      if(!$act){
        return 0;
      }
      $a=strtolower($act);
      switch($a){
        case 'get':
        $this->read($url);
        break;
        case 'post':
        $this->create($url);
        break;
        case 'put':
        $this->replace($url);
        break;
        case 'patch':
        $this->update($url);
        break;
        case 'delete':
        $this->del($url);
        break;
        default:
        return 0;
        break;
      }
      return 0;
    }

    public function main(){
    //which action
    /*-------------------
    Post: create (201, 404, 409 already exists)
    Get: read (200, 404)
    Put: update/Replace (200, 204 no content, 404, not found, 405 method not allowed)
    Patch: update/modify (200, 204 no content, 404, not found, 405 method not allowed)
    Delete: Delete (200, 404 not found, 405 not allowed)
    -------------------*/
    $this->act=$_SERVER['REQUEST_METHOD']?$_SERVER['REQUEST_METHOD']:"";
    $this->urlObj=$this->parseUrl();
      if(array_key_exists("CONTENT_TYPE", $_SERVER) && $_SERVER["CONTENT_TYPE"]=="application/json" && count($_POST)<=0){
      $_POST=json_decode(file_get_contents('php://input'), true);
      }
    }

}


?>
