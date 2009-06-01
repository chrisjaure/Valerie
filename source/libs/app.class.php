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
  Class: App
  
  Static variables and functions for configuring Valerie
*/

class App {

  private static $store = array();
  private static $locked = array();
  
  public static function set($name, $value = null) {
    if (!is_array($name)) {
      if (strpos($name, ':') !== false) {
        list($namespace, $name) = explode(':', $name, 2);
        $name = array($namespace => array($name => $value));
      }
      else {
        $name = array($name => $value);
      }
    }
    foreach ($name as $key => $val) {
      $locked = in_array($key, self::$locked);
      if (is_array($val) && is_array(self::$store[$key])) {
        if ($locked) {
          self::$store[$key] = array_merge($val, self::$store[$key]);
        }
        else {
          self::$store[$key] = array_merge(self::$store[$key], $val);
        }
      }
      else {
        $store = &self::$store[$key];
        $isset = isset($store);
        if (($isset && !$locked) || !$isset) {
          $store = $val;
        }
      }
    }
  }
  
  public static function get($name = null, $namespace = null) {
    if ($name == null) return self::$store;
    if (strpos($name, ':') !== false) {
      list($namespace, $name) = explode(':', $name, 2);
      return self::$store[$namespace][$name];
    }
    elseif (isset($namespace)) {
      return self::$store[$namespace][$name];
    } else {
      return self::$store[$name];
    }
  }
  
  public static function lock($name) {
    if (!is_array($name)) $name = array($name);
    $new_locked = self::$locked + $name;
    self::$locked = array_unique($new_locked);
  }

}
?>
