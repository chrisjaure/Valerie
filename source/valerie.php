<?php

class Valerie {

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
