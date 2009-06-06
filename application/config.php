<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	config.php
//------------------------------------------------------------------------------

App::lock('valerie:config');
App::set('valerie:config', array(


  // BEGIN SETTINGS ------------------------------------------------------------

  'char_encoding' => 'utf-8',
  'session_ns' => 'valerie',
  'app_path' => '../application/',
  'plugin_uri' => '../application/plugins/',
  'source_uri' => '../source/',
  'plugins' => 'all',
  'single_setup' => '../demo/single_setup.php'
  
  // END SETTINGS --------------------------------------------------------------

  
));

$valerie_app_path = realpath(App::get('valerie:config:app_path')) . '/';
App::set('valerie:config', array(

  // BEGIN PATH SETTINGS -------------------------------------------------------

  'plugin_path' => $valerie_app_path . 'plugins/',
  'filter_path' => $valerie_app_path . 'filters/',
  'rule_path' => $valerie_app_path . 'rules/'
  
  // END PATH SETTINGS ---------------------------------------------------------
  
));

?>
