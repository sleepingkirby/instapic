<?php

class posts extends sqlClass{
protected $fileLoc;
  public function __construct($u, $p, $db, $h, $fLc){
    parent::__construct($u, $p, $db, $h);
    $this->fileLoc=$fLc;

  }

  public function __destruct(){
    parent::__destruct();
  }

/*
+----------+---------------+------+-----+---------------------+-------------------------------+
| Field    | Type          | Null | Key | Default             | Extra                         |
+----------+---------------+------+-----+---------------------+-------------------------------+
| id       | bigint(20)    | NO   | PRI | NULL                | auto_increment                |
| usersId  | int(11)       | YES  | MUL | NULL                |                               |
| filePath | varchar(255)  | YES  |     | NULL                |                               |
| w        | int(11)       | YES  |     | NULL                |                               |
| h        | int(11)       | YES  |     | NULL                |                               |
| format   | varchar(10)   | YES  |     | NULL                |                               |
| title    | varchar(100)  | YES  |     | NULL                |                               |
| descrip  | varchar(2000) | YES  |     | NULL                |                               |
| tags     | text          | YES  |     | NULL                |                               |
| datetime | timestamp     | NO   | MUL | current_timestamp() | on update current_timestamp() |
+----------+---------------+------+-----+---------------------+-------------------------------+
*/

  /*---------------------------------
  pre: sqlClass
  post: file moved into proper directory and db entry set
  input: username, tmp file location, title, description, tags (comma delineated)
  output: array. ['status']=bool, ['msg']="string"
  does the "upload"
  which does a few things
  1) moves file to proper place (tmp folder into $this->fileLoc)
  2) make proper sql entry and attach to user. 
  3) make proper entry for tags. Skipping this for the time being.
  4) return success or failure
  ---------------------------------*/
  public function upload($u, $fLoc, $fnm, $title, $descr, $tags){
  $rtrn['status']=false;
  $rtrn['msg']="Upload failed";
    if(!$u||!$fLoc||!$fnm||!$title||!$descr||!$tags){
      $rtrn['msg']="Unable to upload. Required input parameter[s] missing.";
      return $rtrn;
    }
    
    //hardcoding the $_FILES to require ['file'] in the upper level
    if(!is_array($_FILES)||!array_key_exists('file', $_FILES)){
      $rtrn['msg']="File upload failed or not uploaded with expected variables";
      return $rtrn;
    }

    $userInfo=$this->read("select id, UNIX_TIMESTAMP(datetime) as datetime, token, ip, timeout from users where username=\"".$this->escape($u)."\"");

    if(!$userInfo){
    $rtrn['msg']="Upload failed. User doesn't exist.";
    return $rtrn;
    }

    $exArr=explode('.',$fnm);
    $fname=uniqid().'.'.end($exArr);//generating filename

    if(!move_uploaded_file($_FILES['file']["tmp_name"], $this->fileLoc.$fname)){
      $rtrn['msg']="Upload failed when moving file to destionation";
      return $rtrn;
    }
    
    $status=$this->write('insert into img(usersId, filePath, title, descrip, tags, datetime) values('.$userInfo[0]['id'].', "'.$this->fileLoc.$fname.'","'.$title.'", "'.$descr.'", "'.$tags.'", current_timestamp())');

    if(!$status){
    $rtrn['msg']="File uploaded, but unable to make entry in database.";
    return $rtrn;
    }   

    $rtrn['status']=true;
    $rtrn['msg']="File successfully uploaded";
    return $rtrn;
  }

  /*----------------------------------
  pre: sqlClass, $filter (php array from json)
  post: none
  gets all imgs uploaded, allows for sorting by username and/or sorting by time
  $filter['sort']='datetime';
  $filter['usersname']=user name;
  ----------------------------------*/
  public function get($filter){
    $rtrn['status']=false;
    $rtrn['msg']="Unable able to get images";
   
 
    $stmnt="select * from img";
    $sub="";

      if(is_array($filter) && array_key_exists('username', $filter)){
        $userInfo=$this->read("select id, UNIX_TIMESTAMP(datetime) as datetime, token, ip, timeout from users where username=\"".$this->escape($filter['username'])."\"");
        if($userInfo){
        $sub.=" where usersId=".$userInfo[0]['id'];
        }
      }
    
      if(is_array($filter) && array_key_exists('sort', $filter)){
        $sub.=" order by datetime desc";
      }
      $status=$this->read($stmnt.$sub);

      if($status==false){
        $rtrn['msg']="Unable to retrieve from database.";
        return $rtrn;
      }

    $rtrn['status']=true;
    $rtrn['msg']="Image data retrieved.";
    $rtrn['results']=$status;
    return $rtrn;
  }


}

?>
