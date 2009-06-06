<?php
  require_once('../source/bootstrap.php');

  $form = new ValerieServer($_POST);
  
  App::fire('valerie:hooks:beforeValidate', array('form' => &$form));
  
  $data = $form->validate();
  
  App::fire('valerie:hooks:afterValidate', array('form' => &$form, 'data' => &$data));
  
  if ($data) {
    
    App::fire('valerie:hooks:onSuccess', array('form' => &$form, 'data' => &$data));
    
    if (App::get('valerie:form:redirect_on_success')) {
    
      App::fire('valerie:hooks:beforeRedirect', array('form' => &$form, 'data' => &$data));
      
      $form->goto(App::get('valerie:form:redirect_on_success'));
    }
  }
  else {
  
    App::fire('valerie:hooks:onFailure', array('form' => &$form, 'data' => &$data));
    
  }
  
  $form->back();
?>
