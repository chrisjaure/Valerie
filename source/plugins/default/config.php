<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	plugins/default/plugin.php
//------------------------------------------------------------------------------

// $Valerie created in /source/functions.php in newValerieForm.
// setIncludes, setTemplate defined in /source/ValerieForm.php 
$Valerie->setIncludes(array(
  'css' => array('IE', 'ie_style.css')
));

// These functions are called from /source/ValerieForm.php in getOutput.
// They receive an array that contains keys from /source/forms/default_forms.php
// in addition to 'error', 'input', and 'selected'.
$Valerie->setTemplate(array(
  'form_message' => 'default_form_message',
  'field_error' => 'default_field_error',
  'checkbox' => 'default_checkbox',
  'form' => 'default_form',
  'submit' => 'default_submit',
  'text' => 'default_text',
  'password' => 'default_password',
  'textarea' => 'default_textarea',
  'fieldset' => 'default_fieldset',
  'radio' => 'default_radio',
  'select' => 'default_select',
  'checkgroup' => 'default_checkgroup',
  'radiogroup' => 'default_radiogroup',
  'hidden' => 'default_hidden',
  'button' => 'default_button',
  'file' => 'default_file'
));
?>
