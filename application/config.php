<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	config.php
//------------------------------------------------------------------------------

App::set('config', array(


  // BEGIN SETTINGS ------------------------------------------------------------

  'char_encoding' => 'utf-8',
  'session_ns' => 'valerie',
  'app_path' => '../application/',
  'plugin_uri' => '../application/plugins/',
  'source_uri' => '../source/',
  'plugins' => array(
    'contact-form' => array('detect_spam', 'modal'),
    'frm' => 'modal'
  ),
  'single_setup' => '../demo/single_setup.php'
  
  // END SETTINGS --------------------------------------------------------------

  
));

App::set('forms:contact-form:text:modal', 'Contact Me!');
App::set('forms:frm:text:modal', 'Open first form.');

?>
