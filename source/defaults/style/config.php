<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	defaults/style/config.php
//------------------------------------------------------------------------------

include_once 'template.php';

$plugin = array(

  'includes' => array(
    'css' => array('IE', 'ie_style.css')
  ),
  
  'templates' => array(
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
  ),
  
  'uri' => App::get('config:source_uri') . 'defaults/style'
  
);

App::set('styles:default', $plugin);

?>
