<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	config.php
//------------------------------------------------------------------------------


/*
  Class: ValerieConfig
  
  Static variables and functions for configuring Valerie
*/

class ValerieConfig {


  // CONFIG OPTIONS ------------------------------------------------------------

  const ROOT = '';
  const URI = '../source/';
  const CHAR_ENCODING = 'utf-8';
  
  // END CONFIG OPTIONS --------------------------------------------------------
  
  
  private static $root;
  
  public static function root() {
    if (self::ROOT != '') return self::ROOT;
    else {
      if (!isset(self::$root)) {
        self::$root = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . '/';
      }
      return self::$root;
    }
  }

}

?>
