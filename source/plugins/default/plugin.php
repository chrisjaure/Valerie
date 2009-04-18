<?php
$Valerie->setTemplate(array(
  'form_message' => 'default_form_message',
  'field_error' => 'default_field_error',
  'checkbox' => 'default_checkbox',
  'form' => 'default_form',
  'submit' => 'default_submit',
  'text' => 'default_text',
  'password' => 'default_password',
  'textarea' => 'default_textarea',
  'fieldset' => 'default_fieldset',
  'radio' => 'default_radio',
  'select' => 'default_select',
  'checkgroup' => 'default_checkgroup',
  'radiogroup' => 'default_radiogroup',
  'hidden' => 'default_hidden',
  'button' => 'default_button'
));

// form
function default_form($args) {
  extract($args);
  ?>
  
  <form id="<?php echo $id; ?>" class="valerie-form-default" method="<?php echo $method; ?>" action="<?php echo $action; ?>">
    <?php echo $content; ?>
  </form>
  
  <?php
}

// form_message
function default_form_message($args) {
  extract($args);
  ?>
  
  <strong class="valerie-form-message valerie-form-message-<?php echo $type; ?>"><?php echo $message; ?></strong>
  
  <?php
}

// field_error
function default_field_error($args) {
  extract($args);
  ?>
  
  <label class="valerie-field-error" for="<?php echo $id ?>">
    <?php echo $message; ?>
  </label>
  
  <?php
}

//checkbox
function default_checkbox($args) {
  extract($args);
  ?>
  
  <br />
  <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" 
  <?php if ($selected) echo 'checked'; ?>
  /> 
  <label for="<?php echo $id; ?>" class="checkbox"><?php echo $label; ?></label>
  <?php echo $error; ?>
  
  <?php
}

//radio
function default_radio($args) {
  extract($args);
  ?>
  
  <br />
  <input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" 
  <?php if ($selected) echo 'checked'; ?>
  /> 
  <label for="<?php echo $id; ?>" class="checkbox"><?php echo $label; ?></label>
  <?php echo $error; ?>
  
  <?php
}

//checkgroup
function default_checkgroup($args) {
  extract($args);
  ?>
  
  <fieldset>
    <legend id="<?php echo $id; ?>"><?php echo $label; ?><?php echo $error; ?></legend>
    <?php foreach($checkboxes as $checkbox): ?>
      <br />
      <input type="checkbox" id="<?php echo $checkbox['id']; ?>" name="<?php echo $name; ?>" value="<?php echo $checkbox['value']; ?>" 
      <?php if (is_array($input) && in_array($checkbox['value'], $input)) echo 'checked'; ?>
      /> 
      <label for="<?php echo $checkbox['id']; ?>" class="checkbox"><?php echo $checkbox['label']; ?></label>
    <?php endforeach; ?>
  </fieldset>
  
  <?php
}

//radiogroup
function default_radiogroup($args) {
  extract($args);
  ?>
  
  <fieldset>
    <legend id="<?php echo $id; ?>"><?php echo $label; ?><?php echo $error; ?></legend>
    <?php foreach($radios as $radio): ?>
      <br />
      <input type="radio" id="<?php echo $radio['id']; ?>" name="<?php echo $name; ?>" value="<?php echo $radio['value']; ?>" 
      <?php if ($radio['value'] == $input) echo 'checked'; ?>
      /> 
      <label for="<?php echo $radio['id']; ?>" class="checkbox"><?php echo $radio['label']; ?></label>
    <?php endforeach; ?>
  </fieldset>
  
  <?php
}

//text
function default_text($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $input; ?>" />
  <?php echo $error; ?>
  
  <?php
}

//password
function default_password($args) {
  extract($args);
  ?>

  <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <input type="password" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $input; ?>" />
  <?php echo $error; ?>

  <?php
}

//textarea
function default_textarea($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <textarea id="<?php echo $id; ?>" name="<?php echo $name; ?>"><?php echo $input; ?></textarea>
  <?php echo $error; ?>
  
  <?php
}

//select
function default_select($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
    <?php foreach($options as $option): ?>
      <option <?php if ($option['value'] == $input) echo 'selected'; ?> value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
    <?php endforeach; ?>
  </select>
  <?php echo $error; ?>
  
  <?php
}

//hidden
function default_hidden($args) {
  extract($args);
  ?>
  
  <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
  
  <?php
}

//button
function default_button($args) {
  extract($args);
  ?>
  
  <button type="<?php echo $button_type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
    <?php echo $label; ?>
  </button>
  
  <?php
}

//submit
function default_submit($args) {
  extract($args);
  ?>
  
  <input type="submit" value="<?php echo $value; ?>" />
  
  <?php
}

//fieldset
function default_fieldset($args) {
  extract($args);
  ?>
  
  <fieldset>
    <legend><?php echo $legend; ?></legend>
    <?php echo $content; ?>
  </fieldset>
  
  <?php
}

?>
