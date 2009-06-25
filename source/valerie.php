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

  public static function render() {
    $single_form_config = App::get('valerie:form');
    $form = new ValerieForm($single_form_config['plugin']);
    $form->setDefinition($single_form_config['definition']);
    if ($single_form_config['print_assets']) {
      $global = $single_form_config['global'];
      if (!isset($global)) $global = true;
      $form->printAssets($global);
    }
    
    App::fire('valerie:hooks:beforeRender', array('form' => &$form));
    
    $form->render();
    
    App::fire('valerie:hooks:afterRender', array('form' => &$form));
    
  }

}

?>
