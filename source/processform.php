<?php
  $root = realpath(dirname(__FILE__) . '/');
  require_once("$root/bootstrap.php");

  $form = new ValerieServer($_POST);
  
  App::fire('valerie:hooks:beforeValidate', array(&$form));
  
  $data = $form->validate();
  
  App::fire('valerie:hooks:afterValidate', array(&$form, &$data));
  
  if ($data) {
    
    App::fire('valerie:hooks:onSuccess', array(&$form, &$data));
    
    if (App::get('valerie:form:redirect_on_success')) {
    
      App::fire('valerie:hooks:beforeRedirect', array(&$form, &$data));
      
      $form->goto(App::get('valerie:form:redirect_on_success'));
    }
  }
  else {
  
    App::fire('valerie:hooks:onFailure', array(&$form, &$data));
    
  }
  
  $form->back();
?>
