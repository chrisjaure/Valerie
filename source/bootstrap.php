<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	functions.php
//------------------------------------------------------------------------------

/*
  Script: functions.php
*/

require_once "libs/app.class.php";
require_once "config.php";

/*
  Function: newValerieServer
  
  Returns new ValerieServer and includes all rules in /rules dir and all filters
  in /filters dir.
  
  Arguments:
    
    - $data - post data
    
  Returns:
  
    - instance of ValerieServer
*/

function newValerieServer($data) {
  require_once 'valerieserver.php';
  $Valerie = new ValerieServer($data);
  
  $dir = App::get('valerie-config:root');
  foreach(scandir("$dir/rules/") as $file) {
    if (is_file("$dir/rules/$file"))
      include "$dir/rules/$file";
  }
  foreach(scandir("$dir/filters/") as $file) {
    if (is_file("$dir/filters/$file"))
      include "$dir/filters/$file";
  }

  return $Valerie;
}

/*
  Function: newValerieForm
  
  Returns new ValerieForm and includes plugin file.
  
  Arguments:
  
    - $plugin - optional, name of plugin folder
    
  Returns:
  
    - instance of ValerieForm
*/

function newValerieForm($plugin='default') {
  require_once 'valerieform.php';
  $Valerie = new ValerieForm($plugin);
  
  include_once "plugins/$plugin/template.php";
  include "plugins/$plugin/config.php";

  return $Valerie;
}

/*
  Function: __
  
  Translates string if it exists.
  
  Arguments:
  
    - $str - string to translate
  
  Returns:
  
    - translated string or $str
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
  
  Prints translated string if it exists, otherwise prints string.
  
  Arguments:
  
    - $str - string to translate
    
  See Also:
  
    - < __ >
*/

function _e($str) {
    echo __($str);
}

?>
