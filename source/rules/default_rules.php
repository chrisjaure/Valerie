<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	rules/default_rules.php
//------------------------------------------------------------------------------

// $Valerie created in /source/functions.php in newValerieServer.
App::set('valerie-rules', array(

  'required' => array(
    '/^./',
    __('Required.'),
    true
  ),
  
  'int' => array(
    '/^\d+$/', 
    __('Please enter an integer.')
  ),
  
  'alpha' => array(
    '/^[a-z]+$/i',
    __('Please enter letters only.')
  ),
  
  'alphanumeric' => array(
    '/^[a-z\d]+$/i',
    __('Please enter alpha-numeric characters only.')
  ),
  
  'currency' => array(
    '/^(\$|\-|\$\-)?\d{1,3}([,]?\d{3})*(\.\d{2})?$/',
    __('Please enter a US currency.')
  ),
  
  'date' => array(
    '#^(0[1-9]|1[012])[- /\.](0[1-9]|[12][0-9]|3[01])[- /\.](19|20)\d\d#',
    __('Please enter a date.')
  ),
  
  'time' => array(
    '/^([1-9]|0[1-9]|1[0-2]):[0-5]\d[\s]?(am|pm)$/i',
    __('Please enter a 12 hour time value.')
  ),
  
  'time24' => array(
    '/^([0-1]\d|2[0-3]):[0-5]\d$/',
    __('Please enter a 24 hour time value.')
  ),
  
  'phone' => array(
    '/^[\(]?\d{3}[\)]?[\s|\.|-]?\d{3}[\s|\.|-]?\d{4}$/',
    __('Please enter a telephone number.')
  ),
  
  'phoneintl' => array(
    '/^\d{1,3}[\s|\.|-]\d{7,20}$/',
    __('Please enter an international telephone number.')
  ),
  
  'postal' => array(
    '/^([a-z]\d[a-z])[\s|-]?(\d[a-z]\d)$/i',
    __('Please enter a postal code.')
  ),
  
  'zip' => array(
    '/^\d{5}(-\d{4})?$/',
    __('Please enter a zip code.')
  ),
  
  'email' => array(
    '/^([a-z0-9_-]+)(\.[a-z0-9_-]+)*@([a-z0-9_-]+)(\.[a-z0-9_-]+)*[\.]([a-z0-9_-]+)$/i',
    __('Please enter an email address.')
  ),
  
  'url' => array(
    '/^((http|https|ftp):\/\/)?([a-z0-9_-]+)(\.[a-z0-9_-]+)+(\/\w+)*(\.[a-z0-9_-]+)*$/i',
    __('Please enter a URL.')
  ),
  
  'ip' => array(
    '/^(\d{1,3})(\.\d{1,3}){3}$/',
    __('Please enter an IP address.')
  ),
  
  'requiredif' =>array(
    'requiredif',
    __('Required.'),
    true
  ),
  
  'confirm' => array(
    'confirm',
    __('Please enter the same value as {1}.')
  ),
  
  'differ' => array(
    'differ',
    __('Please enter a value different from {1}.')
  ),
  
  'not' => array(
    'notequalto',
    __('Please select a different value.')
  ),
  
  'maxlength' => array(
    'maxlength',
    __('Please enter no more than {1} character(s).')
  ),
  
  'minlength' => array(
    'minlength',
    __('Please enter at least {1} character(s).')
  ),
  
  'rangelength' => array(
    'rangelength',
    __('Please enter between {1} and {2} characters.')
  ),
  
  'selectmin' => array(
    'selectmin',
    __('Please select at least {1} option(s).')
  ),
  
  'selectmax' => array(
    'selectmax',
    __('Please select no more than {1} option(s).')
  ),
  
  'selectrange' => array(
    'selectrange',
    __('Please select between {1} and {2} options.')
  )
));

function requiredif($args, $Valerie) {
  extract($args);
  // $arguments = other field that is checked first
  if (!$Valerie->isEmpty($Valerie->getValue($arguments))) {
    if ($Valerie->isEmpty($value)) return false;
  }
  return true;
}

function confirm($args, $Valerie) {
  extract($args);
  // $arguments = other field that has to be same
  list($fld_name, $label) = $Valerie->getNameLabel($arguments);
  $message = $Valerie->format($error, $label);
  return array($value == $Valerie->getValue($fld_name), $message);
}

function differ($args, $Valerie) {
  extract($args);
  // $arguments = other field that has to differ
  list($fld_name, $label) = $Valerie->getNameLabel($arguments);
  $message = $Valerie->format($error, $label);
  return array($value != $Valerie->getValue($fld_name), $message);
}

function notequalto($args) {
  extract($args);
  // $arguments = value
  return $value != $arguments;
}

function maxlength($args, $Valerie) {
  extract($args);
  // $arguments = max length
  $message = $Valerie->format($error, $arguments);
  return array(utf8_strlen($value) <= (int) $arguments, $message);
}

function minlength($args, $Valerie) {
  extract($args);
  // $arguments = min length
  $message = $Valerie->format($error, $arguments);
  return array(utf8_strlen($value) >= (int) $arguments, $message);
}

function rangelength($args, $Valerie) {
  extract($args);
  // $arguments = [0] min length, [1] max length
  $message = $Valerie->format($error, $arguments);
  $length = utf8_strlen($value);
  return array(
    ($length >= (int) $arguments[0]) && ($length <= (int) $arguments[1]),
    $message
  );
}

function selectmin($args, $Valerie) {
  extract($args);
  // $arguments = min selected number
  $message = $Valerie->format($error, $arguments);
  $count = count($Valerie->getValue($name));
  return array($count >= (int) $arguments, $message);
}

function selectmax($args, $Valerie) {
  extract($args);
  // $arguments = max selected number
  $message = $Valerie->format($error, $arguments);
  $count = count($Valerie->getValue($name));
  return array($count <= (int) $arguments, $message);
}

function selectrange($args, $Valerie) {
  extract($args);
  // $arguments = [0] minimum, [1] maximum
  $message = $Valerie->format($error, $arguments);
  $count = count($Valerie->getValue($name));
  return array(
    ($count >= (int) $arguments[0]) && ($count <= (int) $arguments[1]),
    $message
  );
}

?>
