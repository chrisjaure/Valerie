<?php
  $root = realpath(dirname(__FILE__) . '/');
  require_once("$root/bootstrap.php");

  $form = new ValerieServer($_POST);
  
  $data = $form->validate();

  if ($data) {
  
    if (App::get('valerie:form:redirect_on_success')) {
    
      $form->goto(App::get('valerie:form:redirect_on_success'));
      
    }
    
  }
  
  $form->back();
?>
