# instapic Released under GNU AFFERO GENERAL PUBLIC LICENSE

Requirements: PHP >7.0, mysqli, apache2

Before installing:
Because apache does process http requests for you, restful api URL's that are standard these days need to be setup. This requires apache's rewrite engine. For an example apache site definition, please referr to the extras folder and the site file underneath. It would also be good to properly set CORS if the gui and the backend here are on different servers. This is also set in the apache site definition file.


installation steps:
1) install Apache2, mariadb/mysql, >php7.0, php7.x-json, php7.x-mysqli, php7.x-json
2) set up apache site file if needed. Place file into document root.
3) make sure php is allowed to move uploaded files (should be that way by default.)
4) modify /etc/hosts if needed.
5) set up the SQL table via the db folder's sql dump.
6) configure the variables in the config folder to configure the mariadb username, host and password to connect. Also set the post's file location of where to store theuploaded files.

The below are example commands. They should be self explanatory:
  user commands:
  curl -v -F 'json={"username":"testUser2", "password":"password", "status":"active", "timeout":"50"}' -X POST http://sleepingkirby.local/users/register
  curl -v -F 'json={"password":"password"}' -X POST http://domain/users/username/login
  curl -v -H "Authorization: tokenhere" -H "Username: testUser" -X PATCH http://sleepingkirby.local/users/testUser/loggedIn
  curl -v -H "Authorization: tokenhere" -H "Username: testUser" -X PATCH http://sleepingkirby.local/users/testUser/logout

  post commands:
  curl -F 'json={"title":"testfile", "descr":"testfile description", "tags":"test,test2"}' -F "file=@/home/sleepingkirby/Pictures/EalqtfJXYAswGVP.png" -H "Authorization: tokenhere" -H "Username: testUser" http://sleepingkirby.local/posts/post
  curl -F 'json={"username":"testUser", "sort":"datetime"}' http://sleepingkirby.local/posts/list
  curl http://sleepingkirby.local/posts/3 --output ./tmp.png

*note* the posts/id command needs no validation. This is because, even on a twitter or instagram, the images are usually on CDN's. As such, doesn't require login to see the actual images. This is to mimic that behavior to make sure lists of images can be gotten by img's src= rather that some complex ajax call that stores the binary as base64 in the browser's memory.
