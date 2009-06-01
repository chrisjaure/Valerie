<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	bootstrap.php
//------------------------------------------------------------------------------

/*
  Script: bootstrap.php
*/

require_once "libs/app.class.php";
require_once "config.php";
require_once "valerieserver.php";
require_once "valerieform.php";

$dir = App::get('valerie:config:root');

// load the rules
foreach(scandir("$dir/rules/") as $file) {
  if (is_file("$dir/rules/$file"))
    include_once "$dir/rules/$file";
}

// load the filers
foreach(scandir("$dir/filters/") as $file) {
  if (is_file("$dir/filters/$file"))
    include_once "$dir/filters/$file";
}

// load the plugins
$plugins = App::get('valerie:config:plugins');

if ($plugins = 'all') {
  $plugins = scandir("$dir/plugins/");
}

include_once "$dir/plugins/default/config.php";
foreach ((array) $plugins as $plugin) {    
  if (is_file("$dir/plugins/$plugin/config.php"))
    include_once "$dir/plugins/$plugin/config.php";
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
