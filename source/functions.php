<?php

require_once "config.php";

/*
  Function: newValerieServer
  
  Returns new ValerieServer and includes all rules in /rules dir and all filters
  in /filters dir.
*/

function newValerieServer($data) {
  require('valerieserver.php');
  $Valerie = new ValerieServer($data);
  
  $dir = ValerieConfig::root();
  foreach(scandir($dir.'/rules/') as $file) {
    if (is_file("$dir/rules/$file")) include("$dir/rules/$file");
  }
  foreach(scandir($dir.'/filters/') as $file) {
    if (is_file("$dir/filters/$file")) include("$dir/filters/$file");
  }

  return $Valerie;
}

/*
  Function: newValerieForm
  
  Returns new ValerieForm and includes plugin file.
*/

function newValerieForm($plugin='default') {
  require('valerieform.php');
  $Valerie = new ValerieForm($plugin);
  
  include('plugins/'.$plugin.'/plugin.php');

  return $Valerie;
}

/*
  Function: __
  
  Tranlates string if it exists.
*/

function __($str) {
    global $lang;
    
    if ($lang[$str] != "")
        return $lang[$str];
    else
        return $str;
}

/*
  Function: _e
  
  Prints translated string.
*/
function _e($str) {
    echo __($str);
}

?>
