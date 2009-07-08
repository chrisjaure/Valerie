<?php
$id = 'contact-form';

$config = array(
  
  'definition' => array(
    'attributes' => array(
      'id' => $id
    ),
    'elements' => array(
      array(
        'type' => 'fieldset',
        'label' => __("Contact Us!"),
        'elements' => array(
          array(
            'type' => 'text',
            'label' => __("Your Email Address"),
            'name' => 'cf_email',
            'id' => $id.'-email',
            'validation' => array(
              'required',
              'email'
            ),
            'filter' => 'striptags'
          ),
          array(
            'type' => 'textarea',
            'label' => __("Your Comment"),
            'name' => 'cf_comment',
            'id' => $id.'-comment',
            'validation' => 'required',
            'filter' => array(
              'striptags',
              'wordwrap' => array(70)
            )
          ),
          array(
            'type' => 'submit',
            'label' => 'Send it!'
          )
        )
      )
    )
  )
  
);

App::set("forms:$id", $config);
App::set("hooks:$id", array(
 'onFormValidated' => 'contact_form_send',
 'beforePrintForm' => 'contact_form_check'
));

function contact_form_send(&$form, $data) {
  $to = App::get('config:email');
  if ($to) {
    $from = $data['cf_email'];
    $subject = _("Comment from") . " $from";
    if (!mail($to, $subject, $data['cf_comment'], "From: $from")) {
      $form->setResponse('form:message', __('An error ocurred. Please try again later.'));
      $form->setResponse('form:message_type', 'error');
    }
  }
}

function contact_form_check($form, &$output) {
  if (!App::get('config:email')) {
    $output = '<p><b>Error</b>: Please set an email address at "config:email" before using this form.</p>';
  }
}
?>
