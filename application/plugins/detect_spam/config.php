<?php

App::set('hooks', array(
  'afterValidateForm' => 'detect_spam',
  'beforePrintForm' => 'detect_spam_addField',
  'afterPrintAssets' => 'detect_spam_style'
));

function detect_spam(&$form, &$data, $valid) {
  if ($valid) {
    $spam = false;
    
    if ($data['ds_email'] != '') {
      $spam = true;
    }
    
    $data['spam'] = $spam;
    if ($spam) {
      if (App::get('config:discard_spam')) {
        $form->back(
          __("Your submission has been discarded because it is suspected spam."),
          'error'
        );
      }
      else {
        $form->setResponse(
          'form:message',
          __("Your submission has been flagged as spam.")
        );
        $form->setResponse('form:message_type', 'error');
      }
    }
  }
  unset($data['ds_email']);
}

function detect_spam_addField(&$form) {
  $form->setDefinition(array(
    'elements' => array(
      array(
        'type' => 'spamtrap',
        'name' => 'ds_email',
        'id' => 'detect-spam-' . rand(),
        'label' => __("Do not fill out this field.")
      )
    )
  ));
  
  $form->setTemplate(array(
    'spamtrap' => 'detect_spam_field'
  ));
}

function detect_spam_field($args) {
  extract($args);
  ?>
  
  <label class="spamtrap"><?php echo $label; ?>
    <input type="text" class="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" />
  </label>
  
  <?php
}

function detect_spam_style() {
  ?>
  
  <style>
    form .spamtrap {
      display:none;
    }
  </style>
  
  <?php
}

App::set('config:discard_spam', false);

?>
