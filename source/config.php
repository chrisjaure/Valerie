<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	config.php
//------------------------------------------------------------------------------

AppConfig::set(array(


  // BEGIN SETTINGS ------------------------------------------------------------

  'root' => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . '/',
  'uri' => '../source/',
  'char_encoding' => 'utf-8',
  'session_ns' => 'valerie'
  
  // END SETTINGS --------------------------------------------------------------

  
), 'valerie');
?>
