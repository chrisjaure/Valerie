<?php

$modal_id = 'modal-'.rand();

App::set('plugins:modal:hooks', array(
  'afterPrintAssets' => 'modal_assets',
  'beforePrintForm' => 'modal_wrapper_start',
  'afterPrintForm' => 'modal_wrapper_end',
  'afterPrintSubmit' => 'modal_cancel',
  'afterPrintButton' => 'modal_cancel'
));

function modal_assets() {
  if (!App::get('plugin_assets_printed:modal')) {
    ?>
    
    <link rel="stylesheet" type="text/css" href="<?php echo App::get('config:plugin_uri'); ?>modal/style.css" />
    <script type="text/javascript" src="<?php echo App::get('config:plugin_uri'); ?>modal/script.js"></script>
    
    <?php
    App::set('plugin_assets_printed:modal', true);
  }
}

function modal_cancel() {
  ?>
  
  <a href="#" class="modal-cancel">cancel</a>
  
  <?php
}

function modal_wrapper_start() {
  ?>
  <script type="text/javascript">
  jQuery(function($){
    $('#<?php echo App::get('form_id'); ?>').bind('valerie.formValidated', function(){
      setTimeout(modal.hideForm, 1500);
    });
  });
  </script>
  <a href="#" class="modal-show">Form</a>
  <div class="modal-wrapper">
  
  <?php
}

function modal_wrapper_end() {
  ?>
  
  </div>
  
  <?php
}

?>
