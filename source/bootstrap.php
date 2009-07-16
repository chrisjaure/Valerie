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

$time = microtime();
$time = explode(' ', $time);
$valerie_start_time = $time[1] + $time[0];

@session_start();

// path to configuration file
$valerie_config_path = '../application/config.php';

$valerie_root = realpath(dirname(__FILE__) . '/') . '/';

require_once "libs/app.class.php";
App::set('config:version', '0.8');

require_once $valerie_config_path;

App::lock('config');
$valerie_app_path = realpath(App::get('config:app_path')) . '/';
$valerie_app_uri = App::get('config:app_uri');

// set some variables after config is loaded so they can be set first in the
// config file.
App::set('config', array(
  'style_path' => $valerie_app_path . 'styles/',
  'plugin_path' => $valerie_app_path . 'plugins/',
  'filter_path' => $valerie_app_path . 'filters/',
  'rule_path' => $valerie_app_path . 'rules/',
  'form_path' => $valerie_app_path . 'forms/',
  'style_uri' => $valerie_app_uri . 'styles/',
  'plugin_uri' => $valerie_app_uri . 'plugins/',
  'form_uri' => $valerie_app_uri . 'forms/',
  'root' => $valerie_root
));

$valerie_root = App::get('config:root');

require_once "valerieserver.php";
require_once "valerieform.php";
require_once "valerie.php";

Valerie::startProfiler($valerie_start_time);

// load defaults
include_once "{$valerie_root}defaults/style/config.php";
include_once "{$valerie_root}defaults/filters.php";
include_once "{$valerie_root}defaults/rules.php";

// load the styles, rules, filters, and forms
$valerie_paths = array(
  App::get('config:style_path'),
  App::get('config:rule_path'),
  App::get('config:filter_path'),
  App::get('config:form_path')
);
valerie_load_dir_files($valerie_paths);

// load the plugins
$valerie_plugin_path = App::get('config:plugin_path');
$valerie_plugins = App::get('config:plugins');

if ($valerie_plugins == 'all') {
  if (is_dir($valerie_plugin_path)) {
    $valerie_plugins = scandir($valerie_plugin_path);
  }
}

foreach ((array) $valerie_plugins as $form_id => $valerie_plugin) {
  if (is_int($form_id)) {
    if (is_file($valerie_plugin_path . $valerie_plugin .'/config.php')) {
      include_once $valerie_plugin_path . $valerie_plugin .'/config.php';
    }
    if (is_file($valerie_plugin_path . $valerie_plugin . '.php')) {
      include_once $valerie_plugin_path . $valerie_plugin . '.php';
    }
    if (is_file($valerie_plugin_path . $valerie_plugin)) {
      include_once $valerie_plugin_path . $valerie_plugin;
    }
    
    // attach hooks
    $valerie_plugin_hooks = App::get("plugins:$valerie_plugin:hooks");

    if (is_array($valerie_plugin_hooks)) {
      foreach ($valerie_plugin_hooks as $hook => $fn) {
        App::attach("hooks:$hook", $fn);
      }
    }
  }
  else {
    Valerie::loadFormPlugins($form_id);
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
        if (is_file($dir . $file .'/config.php')) {
          include_once $dir . $file .'/config.php';
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
