<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	bootstrap.php
//------------------------------------------------------------------------------

/*
  Script: bootstrap.php
*/

@session_start();

// path to configuration file
$valerie_config_path = '../application/config.php';

$valerie_root = realpath(dirname(__FILE__) . '/') . '/';

require_once "libs/app.class.php";
require_once $valerie_config_path;

App::lock('config');
$valerie_app_path = realpath(App::get('config:app_path')) . '/';

// set some variables after config is loaded so they can be set first in the
// config file.
App::set('config', array(
  'version' => '0.7.1',
  'style_path' => $valerie_app_path . 'styles/',
  'plugin_path' => $valerie_app_path . 'plugins/',
  'filter_path' => $valerie_app_path . 'filters/',
  'rule_path' => $valerie_app_path . 'rules/',
  'root' => $valerie_root
));

$valerie_root = App::get('config:root');

require_once "valerieserver.php";
require_once "valerieform.php";
require_once "valerie.php";

// load defaults
include_once "{$valerie_root}defaults/style/config.php";
include_once "{$valerie_root}defaults/filters.php";
include_once "{$valerie_root}defaults/rules.php";

// load the styles, rules, and filters
$valerie_paths = array(
  App::get('config:style_path'),
  App::get('config:rule_path'),
  App::get('config:filter_path')
);
valerie_load_dir_files(array($valerie_paths));

// load the plugins
$valerie_plugin_path = App::get('config:plugin_path');
$valerie_plugins = App::get('config:plugins');

if ($valerie_plugins = 'all') {
  if (is_dir($valerie_plugin_path)) {
    $valerie_plugins = scandir($valerie_plugin_path);
  }
}

foreach ((array) $valerie_plugins as $valerie_plugin) {    
  if (is_file($valerie_plugin_path . $valerie_plugin .'/config.php')) {
    include_once $valerie_plugin_path . $valerie_plugin .'/config.php';
  }
  if (is_file($valerie_plugin_path . $valerie_plugin)) {
    include_once $valerie_plugin_path . $valerie_plugin;
  }
}

// load the single setup config if set
if (App::get('config:single_setup')) {
  include_once $valerie_root . App::get('config:single_setup');
}

/*
  Function: valerie_load_dir_files

  Includes files in a directory.
  
  Arguments:
  
    $dirs - string or array of dirs
*/

function valerie_load_dir_files($dirs) {
  foreach ((array) $dirs as $dir) {
    if (is_dir($dir)) {
      foreach(scandir($dir) as $file) {
        if (is_file($dir . $file)) {
          include_once $dir . $file;
        }
      }
    }
  }
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
