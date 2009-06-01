<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	config.php
//------------------------------------------------------------------------------

App::lock('valerie-config');
App::set('valerie-config', array(


  // BEGIN SETTINGS ------------------------------------------------------------

  'root' => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . '/',
  'uri' => '../source/',
  'char_encoding' => 'utf-8',
  'session_ns' => 'valerie',
  'plugins' => 'all'
  
  // END SETTINGS --------------------------------------------------------------

  
));
?>
