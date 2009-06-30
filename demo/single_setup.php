<?php

$config = array(

  'definition' => array(
    'attributes' => array(
      'id' => 'frm3'
    ),
    'elements' => array(
      array(
        'type' => 'fieldset',
        'legend' => 'This is the last form. It has a single file setup.',
        'elements' => array(
          array(
            'type' => 'text',
            'label' => 'Enter something cool.',
            'name' => 'cool',
            'id' => 'fld_cool',
            'validation' => 'required',
            'filter' => 'striptags'
          ),
          array(
            'type' => 'submit',
            'value' => 'This is cool!'
          )
        )
      )
    )
  ),
  'plugin' => 'default',
  'redirect_on_success' => false,
  'print_assets' => false
);

App::set('valerie:form', $config);

App::attach('valerie:hooks:frm3', array(

  'onSuccess' => 'myform_success',
  'beforeRender' => 'myform_beforeRender',
  'afterRender' => 'myform_afterRender'

));

function myform_success(&$form) {
  $form->setResponse('form:message', 'That is sort of cool, I guess...');
}

function myform_beforeRender() {
  echo '<p>I attached this beforeRender event!</p>';
}

function myform_afterRender() {
  echo '<p>I attached this afterRender event, too! Pretty sweet, huh?';
}
?>