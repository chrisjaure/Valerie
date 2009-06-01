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

$valerie_root = App::get('valerie:config:root');

// load defaults
include_once "$valerie_root/defaults/plugin/config.php";
include_once "$valerie_root/defaults/filters.php";
include_once "$valerie_root/defaults/rules.php";

// load the rules
$valerie_rule_path = App::get('valerie:config:rule_path');
foreach(scandir($valerie_rule_path) as $file) {
  if (is_file($valerie_rule_path . $file))
    include_once $valerie_rule_path . $file;
}

// load the filers
$valerie_filter_path = App::get('valerie:config:filter_path');
foreach(scandir($valerie_filter_path) as $file) {
  if (is_file($valerie_filter_path . $file))
    include_once $valerie_filter_path . $file;
}

// load the plugins
$valerie_plugin_path = App::get('valerie:config:plugin_path');
$valerie_plugins = App::get('valerie:config:plugins');

if ($valerie_plugins = 'all') {
  $valerie_plugins = scandir($valerie_plugin_path);
}

foreach ((array) $valerie_plugins as $valerie_plugin) {    
  if (is_file($valerie_plugin_path . $valerie_plugin .'/config.php'))
    include_once $valerie_plugin_path . $valerie_plugin .'/config.php';
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
