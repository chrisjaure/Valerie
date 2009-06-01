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
      $name = array($name => $value);
    }
  
    foreach ($name as $key => $value) {
      $namespace = explode(':', $key);
      $store = &self::$store;
      $locked = false;
      $nscheck = '';
      foreach ($namespace as $ns) {
        $store = &$store[$ns];
        $nscheck = ltrim($nscheck . ':'. $ns, ':');
        if (in_array($nscheck, self::$locked)) {
          $locked = true;
        }
      }
      if (isset($store)) {
        if (is_array($value) && is_array($store)) {
          foreach($value as $new_key => $val) {
            self::set($key.$new_key, $val);
          }
        } else {
          if (!$locked) {
            $store = $value;
          }
        }
      }
      else {
        $store = $value;
      }
    } 
  }
  
  public static function get($name = null, $namespace = null) {
    if ($name == null) return self::$store;
    $name = explode(':', $name);
    
    if (isset($namespace)) {
      $name = array_merge(explode(':', $namespace), $name);
    }
    
    $store = self::$store;
    foreach ($name as $ns) {
      $store = $store[$ns];
    }
    
    return $store;
  }
  
  public static function lock($name) {
    $new_locked = self::$locked + (array) $name;
    self::$locked = array_unique($new_locked);
  }

}
?>
