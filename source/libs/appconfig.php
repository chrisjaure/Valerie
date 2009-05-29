<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	appconfig.php
//------------------------------------------------------------------------------


/*
  Class: AppConfig
  
  Static variables and functions for configuring Valerie
*/

class AppConfig {

  private static $settings = array();
  
  public static function set($name, $value = null, $namespace = null) {
    if (!is_array($name)) {
      $name = array($name, $value);
    }
    else {
      $namespace = $value;
    }
    if (isset($namespace)) {
      $ref = &self::$settings[$namespace];
      if (!is_array($ref)) $ref = array();
    }
    else {
      $ref = &self::$settings;
    }
    foreach ($name as $key => $val) {
      if (strpos($key, ':') !== false) {
        list($namespace, $key) = explode(':', $key, 2);
        if (!isset(self::$settings[$namespace][$key])) {
          self::$settings[$namespace][$key] = $val;
        }
      }
      else {
        if (!isset($ref[$key])) {
          $ref[$key] = $val;
        }
      }
    }
  }

  public static function get($name, $namespace = null) {
    if (strpos($name, ':') !== false) {
      list($namespace, $name) = explode(':', $name, 2);
    }
    if (isset($namespace)) {
      $ref = &self::$settings[$namespace];
    }
    else {
      $ref = &self::$settings;
    }
    return $ref[$name];
  }

}
?>
