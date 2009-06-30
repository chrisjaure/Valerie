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
      $single_form_config = App::get("valerie:forms:$name");
      if (!isset($single_form_config)) {
        trigger_error("No form found at 'valerie:forms:$name'", E_USER_ERROR);
      }
    }
    else {
      $single_form_config = App::get('valerie:form');
      if (!isset($single_form_config)) {
        trigger_error("No form found at 'valerie:form'", E_USER_ERROR);
      }
    }
    
    $single_form_config['definition']['attributes'] += array(
      'method' => 'post',
      'action' => App::get('valerie:config:source_uri') . 'processform.php'
    );
    
    $renderer = $single_form_config['plugin'];
    if (!isset($renderer)) {
      $renderer = 'default';
    }
    $form = new ValerieForm($renderer);
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
    $renderer = App::get('valerie:config:plugin');
    $id = App::get('valerie:form_id');
    App::fire("valerie:hooks:$hook", $args);
    App::fire("valerie:hooks:$renderer:$hook", $args);
    App::fire("valerie:hooks:$id:$hook", $args);
  }

}

?>
