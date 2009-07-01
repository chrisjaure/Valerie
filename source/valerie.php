<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	bootstrap.php
//------------------------------------------------------------------------------

/*
  Class: Valerie
  
  Contains static methods related to Valerie
*/

class Valerie {

  /*
    Method: render
    
    A shortcut to render the form defined with the config 'single_setup'
  */  

  public static function render($name = null) {
    if (isset($name)) {
      $single_form_config = App::get("forms:$name");
      if (!isset($single_form_config)) {
        trigger_error("No form found at 'forms:$name'", E_USER_ERROR);
      }
    }
    else {
      $single_form_config = App::get('form');
      if (!isset($single_form_config)) {
        trigger_error("No form found at 'form'", E_USER_ERROR);
      }
    }
    
    $single_form_config['definition']['attributes'] += array(
      'method' => 'post',
      'action' => App::get('config:source_uri') . 'processform.php'
    );
    
    $style = $single_form_config['style'];
    if (!isset($style)) {
      $style = 'default';
    }
    $form = new ValerieForm($style);
    $form->setDefinition($single_form_config['definition']);
    if ($single_form_config['print_assets']) {
      $global = $single_form_config['global'];
      if (!isset($global)) $global = true;
      $form->printAssets($global);
    }
    $form->render();
  }
  
  /*
    Method: fireHooks
    
    Fires generic hook, form hook, and plugin hook
  */
  
  public static function fireHooks($hook, $args = null) {
    $renderer = App::get('config:renderer');
    $id = App::get('form_id');
    App::fire("hooks:$hook", $args);
    App::fire("hooks:$renderer:$hook", $args);
    App::fire("hooks:$id:$hook", $args);
  }

}

?>
