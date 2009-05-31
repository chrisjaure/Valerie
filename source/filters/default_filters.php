<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	filters/default_filters.php
//------------------------------------------------------------------------------

// $Valerie created in /source/functions.php in newValerieServer.
$Valerie->registerFilters(array(
  'striptags' => 'strip_tags',
  'purify' => 'default_purify',
  'limit' => 'default_limit'
));


/*
  Function: default_purify
  
  Before using this filter, please read
  http://htmlpurifier.org/download.html#Installation
  
  You may need to set some options.
*/
function default_purify($value, $args) {
  require_once App::get('valerie-config:root')
    . 'libs/htmlpurifier/HTMLPurifier.standalone.php';
  
  $config = HTMLPurifier_Config::createDefault();
  
  // Options for the demo
  $config->set('Core', 'AggressivelyFixLt', true);
  $config->set('HTML', 'Doctype', 'XHTML 1.0 Strict');
  
  $purifier = new HTMLPurifier($config);
  
  return $purifier->purify($value);
}

function default_limit($value, $limit) {
  return utf8_substr($value, 0, $limit);
}

?>
