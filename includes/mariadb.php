<?php  
/*------------------mysql class-----------------------------------------------*/
class sqlClass{
  private $u;
  private $p;
  protected $db;
  protected $host;
  public $obj;

  function __construct($user, $pass,$datab,$host='localhost'){
    $this->u=$user;
    $this->p=$pass;
    $this->db=$datab;
    $this->host=$host;
    $this->obj=mysqli_connect($this->host, $this->u, $this->p, $this->db);

      if(!$this->obj){
  	    error_log(mysqli_connect_errno());
	      error_log($this->obj->error);
	      exit(1);
      }

      if (!mysqli_set_charset($this->obj, "utf8")) {
      error_log("Unable to set mysqli character set: ".mysqli_error($this->obj));
      exit(1);
      }
  } 

  function __destruct(){
    mysqli_close($this->obj);
  }

/*------------------------------------------------
pre: $query
post: mysql results from $query
connects and reads the results from the query and returns it in an array
-------------------------------------------------*/
  function read($query){
    
    $results=mysqli_query($this->obj,$query);
      if(!$results){
        return 0;
      }

    $x=0;
    $temp=mysqli_fetch_assoc($results);
      if($temp){
        while($temp){
          $r[$x]=$temp;
          $x++;
          $temp=mysqli_fetch_assoc($results);
        }
      }
      else{
        return NULL;
      }
    mysqli_free_result($results);
     
    return $r;
  }



/*---------------------------------------------------------------
pre: $query
post: results, if any

---------------------------------------------------------------*/  
  function write($query){
    $results=mysqli_query($this->obj,$query);
    $error=mysqli_error($this->obj);
    if($error){
      return $error;
    } 
    return $results;
  }



/*------------------------
pre: string to escape
post: none
escapes query
------------------------*/
  function escape($str){
    return mysqli_real_escape_string($this->obj,$str);
  }

/*------------------------
pre: $u, $debug
post: true (1), false(0), array if $debug is set
Checks the string inputted into $u. If any of the characters are in the array, return true.
------------------------*/
  function escapecheck($u,$debug){
  //check for illegal characters. If there is, return -5
    $illegal_char[0]=";";
    $illegal_char[1]="&";
    $illegal_char[2]="'";
    $illegal_char[3]="\"";
    $illegal_char[4]="\\";
    $illegal_char[5]="|";
    if($debug){
      return $illegal_char;
    }
    while(current($illegal_char)){
      //if the character is in the user name
      if(strpos($u,current($illegal_char))){
        return "1";
      }
     //else, move array up one step and try again
      else{
        next($illegal_char);
      }
    }
    return 0;
  }
}

?>
