<?php
/*---------------------------------------------
pre: none
post: none
inputs $kArr (array of keys), $arr (original array)
Returns false if any of the values in $kArr doesn't
exist as key in $arr.
If all values in $kArr exists, in $arr, returns true
---------------------------------------------*/
function array_keys_exists($kArr, $arr){
  if(!is_array($kArr)||!is_array($arr)){
  return false;
  }

  reset($kArr);
  $tmp=key($kArr);
  while($kArr[$tmp]){
    if(!array_key_exists($kArr[$tmp],$arr)){
    return false;
    }
  next($kArr);
  $tmp=key($kArr);
  }
  return true;
}

?>
