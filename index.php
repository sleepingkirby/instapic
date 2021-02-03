<?php
require('./includes/urlParse.php');
require('./includes/auth.php');
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
$url->main();
$url->varDump();



//auth module
/*
  If a user is not logged in, the only actions available are
  a) login (/users/<id>/login, POST )
  b) register (/users/register, POST)

  if user is logged in, they can
  a) upload (/posts/post, POST), modify posts(/posts/post/<id>, PUT)
  b) get a list of all posts (/posts/list , GET)
  c) logout (/users/<id>/logoff, PATCH)


login();//also handles relogin
isLoggedIn(); //keep alive
logout();//clear session data
*/
$auth=new auth($_db['user'], $_db['pass'], $_db['db'], $_db['host'], $_auth['tknLen']);

var_dump($auth->loggedIn("testUser", 'cf354b38bda38a10172b7ae3c25a5390d8dbc60803375e8a5a'));

//data retriever





?>
