<?php

App::set('plugins:modal', array(
  'hooks' => array(
    'beforePrintForm' => 'modal_wrapper_start',
    'afterPrintForm' => 'modal_wrapper_end',
    'afterPrintSubmit' => 'modal_cancel',
    'afterPrintButton' => 'modal_cancel'
  ),
  'assets' => array(
    'css' => 'style.css',
    'js' => 'script.js'
  )
));

function modal_cancel() {
  ?>
  
  <a href="#" class="modal-cancel">cancel</a>
  
  <?php
}

function modal_wrapper_start() {
  $id = App::get('form_id');
  $link = App::get("forms:$id:text:modal");
  ?>
  <script type="text/javascript">
  jQuery(function($){
    $('#<?php echo App::get('form_id'); ?>').bind('valerie.formValidated', function(){
      setTimeout(modal.hideForm, 1500);
    });
  });
  </script>
  <a href="#" class="modal-show"><?php echo $link; ?></a>
  <div class="modal-wrapper">
  
  <?php
}

function modal_wrapper_end() {
  ?>
  
  </div>
  
  <?php
}

?>
