<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	app.class.php
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
        $new_val_is_array = is_array($value);
        $old_val_is_array = is_array($store);
        if ($new_val_is_array && $old_val_is_array) {
          $indexed = true;
          foreach ($value as $new_key => $val) {
            if (!is_int($new_key)) {
              self::set($key . ':' . $new_key, $val);
              $indexed = false;
            }
          }
          foreach ($store as $old_key => $val) {
            if (!is_int($old_key)) {
              $indexed = false;
            }
          }
          if ($indexed) {
            if(!$locked) {
              $store = $value;
            }
          }
        }
        elseif (!$new_val_is_array && $old_val_is_array) {
          if (!self::child_locked($key)) {
            $store = $value;
          }
        }
        else {
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
  
  public static function get($name = null, $callback = null, $args = null) {
    $store = self::$store;
    
    if (isset($name)) {
      $name = explode(':', $name);
      foreach ($name as $ns) {
        $store = $store[$ns];
      }
    }
    
    if (isset($callback) && function_exists($callback)) {
      if (is_array($store)) {
        array_walk($store, $callback, $args);
      }
      else {
        call_user_func($callback, $store, $args);
      }
    }
    
    return $store;
  }
  
  public static function fire($name, $args) {
    $store = self::$store;
    $name = explode(':', $name);
    foreach ($name as $ns) {
      $store = $store[$ns];
    }
    foreach ((array) $store as $fn) {
      if (is_callable($fn)) {
        call_user_func($fn, $args);
      }
    }
  }
  
  public static function attach($name, $item) {
    if (is_array($item)) {
      foreach($item as $key => $val) {
        self::attach($name . ':' . $key, $val);
      }
    }
    else {
      $value = (array) App::get($name);
      $value[] = $item;
      App::set($name, $value);
    }
  }
  
  public static function detach($name, $item) {
    $value = (array) App::get($name);
    $index = array_search($item, $value);
    unset($value[$index]);
    App::set($name, $value);
  }
  
  public static function lock($name) {
    $new_locked = self::$locked;
    $new_locked[] = $name;
    self::$locked = array_unique($new_locked);
  }
  
  private static function child_locked($name) {
    $vals = self::get($name);
    if (is_array($vals)) {
      foreach ($vals as $key => $val) {
        if(self::child_locked($name . ':' . $key)) return true;
      }
    }
    return (in_array($name, self::$locked));
  }

}
?>
