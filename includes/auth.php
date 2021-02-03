<?php
require('./includes/mariadb.php');

class auth extends sqlClass{
public $tknLen;

  public function __construct($u, $p, $db, $h, $tl){
    parent::__construct($u, $p, $db, $h);
    $this->tknLen=$tl;
  }

  public function __destruct(){
    parent::__destruct();
  }


  /*--------------------------------------------------------
  pre: none
  post: none
  sends random string back 
  --------------------------------------------------------*/
  public function genTkn($len=0){
  $l=$len;
    if(!$l || $l<=0){
      $l=$this->tknLen;
    }
  $bin = random_bytes($l);
  return bin2hex($bin);
  }

  /*---------------------------------------------------------
  pre: sqlClass, genTkn();
  post: user session created (token made, user table updated)
  if password is valid, set user session and return object with status and token
  if not, return bad status and message
  ---------------------------------------------------------*/
  public function login($u, $p){
    $rtrn=array("status"=>false, "msg"=>"Login failed.", "tkn"=>"");

    if(!$u || !$p || $p=="" || $u==""){
      $rtrn['msg']="No username or password provided";
      return $rtrn;
    }

    $userInfo=$this->read("select id, password, status, datetime, ip, timeout from users where username=\"".$this->escape($u)."\"");

    //if user doesn't exist
    if(!$userInfo){
      $rtrn['status']=false;
      $rtrn['msg']='username is not found';
      return $rtrn;
    }

    //if user isn't active
    if($userInfo[0]['status']!="active"){
      $rtrn['status']=false;
      $rtrn['msg']='username is not active';
      return $rtrn;
    }

 
    if(password_verify($p, $userInfo[0]['password'])){
      $tkn=$this->genTkn();
      $back=$this->write("update users set datetime=current_timestamp(), ip=\"".$_SERVER['REMOTE_ADDR']."\", last_login=current_timestamp(), token=\"".$tkn."\" where id=".$this->escape($userInfo[0]['id']));
        if($back==false){
        $rtrn['status']=false;
        $rtrn['msg']=$this->obj->error;
        return $rtrn;
        }

      $rtrn['status']=true;
      $rtrn['msg']="Logged in";
      return $rtrn;
    }
    else{
      $rtrn['msg']="Password incorrect.";
      //if this weren't just a test, I would make a login time out for too many bad logins.
      return $rtrn;
    } 
    
    return $rtrn;
  }


  /*---------------------------------------------------------
  pre: sqlClass, genTkn();
  post: user session created (token made, user table updated)
  if password is valid, set user session and return token
  ---------------------------------------------------------*/
  public function logout($u){
    $rtrn['status']=false;
    $rtrn['msg']="Logout failed";

    if(!$u){
    $rtrn['msg']="no username provided. Unable to log out";
    return $rtrn;
    }

    if($this->write("update users set token=\"\", ip=\"\" where username=\"".$this->escape($u)."\"")){
    $rtrn['status']=true;
    $rtrn['msg']="Logged out successfully.";
    return $rtrn;    
    }
 

    return $rtrn;
  }


  /*---------------------------------------------------------
  pre: sqlClass
  post: update session
  checks if session is still valid. i.e:
  1) ip is still the same
  2) token still exists and is the same
  3) time out hasn't happened
  ---------------------------------------------------------*/
  public function loggedIn($u, $tkn){
    $rtrn['status']=false;
    $rtrn['msg']="";
    if(!$u||!$tkn){
      $rtrn['msg']="Username and/or token provided.";
      return $rtrn;
    }    

    $userInfo=$this->read("select id, UNIX_TIMESTAMP(datetime) as datetime, token, ip, timeout from users where username=\"".$this->escape($u)."\"");
    if($userInfo==null ||$userInfo==false){
      $rtrn['msg']="Unable to find username";
      return $rtrn;
    }

    //username is right, but token is wrong. Can be a spoofing attempt. Do nothing. Tell the attempt that  
    if($userInfo[0]['token']!=$tkn || $userInfo[0]['ip']!=$_SERVER['REMOTE_ADDR']){
      $rtrn['msg']="Session not valid.";
      return $rtrn;
    }

    //if here, info provided is good. Just need to make sure it's STILL valid
    //time out logic. datetime is returned as unix timestamp
    if(time() >= $userInfo[0]['datetime']+($userInfo[0]['timeout']*60)){
      $rtrn['msg']="Session timed out.";
      $this->logout();
      return $rtrn;
    }

    //why explicitly match? Because I'm paranoid. This also guarantees that the logic is solid and I'm not missing edge cases.
    if($userInfo[0]['token']==$tkn && $userInfo[0]['ip']==$_SERVER['REMOTE_ADDR'] && (time() < $userInfo[0]['datetime']+($userInfo[0]['timeout']*60))){
      $back=$this->write('update users set datetime=current_timestamp() where id='.$userInfo[0]['id']);
      if($back==false){
        $rtrn['msg']="Keep alive failed.";
        return $rtrn;
      }
      $rtrn['status']=true;
      $rtrn['msg']="Session continued";
      return $rtrn;
    }

    return $rtrn;
  }


}


?>
