<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	plugins/default/plugin.php
//------------------------------------------------------------------------------

// $Valerie created in /source/functions.php in newValerieForm.
// setIncludes, setTemplate defined in /source/ValerieForm.php 
$Valerie->setIncludes(array(
  'css' => array('IE', 'ie_style.css')
));

// These functions are called from /source/ValerieForm.php in getOutput.
// They receive an array that contains keys from /source/forms/default_forms.php
// in addition to 'error', 'input', and 'selected'.
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
  'button' => 'default_button',
  'file' => 'default_file'
));

// form
function default_form($args) {
  extract($args);
  ?>
  
  <form
    id="<?php echo $id; ?>"
    class="valerie-form-default"
    method="<?php echo $method; ?>"
    action="<?php echo $action; ?>"
  >
    <?php echo $message; ?>
    <?php echo $content; ?>
  </form>
  
  <?php
}

// form_message
function default_form_message($args) {
  extract($args);
  ?>
  
  <div class="valerie-form-message valerie-form-message-<?php echo $type; ?>">
    <?php echo $message; ?>
  </div>
  
  <?php
}

// field_error
function default_field_error($args) {
  extract($args);
  ?>
  
  <span class="valerie-field-error">
    <?php echo $message; ?>
  </span>
  
  <?php
}

// checkbox
function default_checkbox($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>">
    <input
      type="checkbox"
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      value="<?php echo $value; ?>"
      class="checkbox"
      <?php if ($selected) echo 'checked'; ?>
    /> 
    <?php echo $label; ?>
    <?php echo $error; ?>
  </label>
  
  <?php
}

// radio
function default_radio($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>">
    <input
      type="radio"
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      value="<?php echo $value; ?>"
      class="radio"
      <?php if ($selected) echo 'checked'; ?>
    /> 
    <?php echo $label; ?>
    <?php echo $error; ?>
  </label>
  
  <?php
}

// checkgroup
function default_checkgroup($args) {
  extract($args);
  ?>
  
  <fieldset <?php if (isset($error)) echo 'class="valerie-alert"'; ?>>
    <legend id="<?php echo $id; ?>">
      <?php echo $label; ?>
      <?php echo $error; ?>
    </legend>
    <?php foreach($checkboxes as $checkbox): ?>
      <label for="<?php echo $checkbox['id']; ?>">
        <input
          type="checkbox"
          id="<?php echo $checkbox['id']; ?>"
          name="<?php echo $name; ?>"
          value="<?php echo $checkbox['value']; ?>"
          class="checkbox"
          <?php if (is_array($input) && in_array($checkbox['value'], $input)) echo 'checked'; ?>
        /> 
        <?php echo $checkbox['label']; ?>
      </label>
    <?php endforeach; ?>
  </fieldset>
  
  <?php
}

// radiogroup
function default_radiogroup($args) {
  extract($args);
  ?>
  
  <fieldset <?php if (isset($error)) echo 'class="valerie-alert"'?>>
    <legend id="<?php echo $id; ?>">
      <?php echo $label; ?>
      <?php echo $error; ?>
    </legend>
    <?php foreach($radios as $radio): ?>
      <label for="<?php echo $radio['id']; ?>">
        <input
          type="radio"
          id="<?php echo $radio['id']; ?>"
          name="<?php echo $name; ?>"
          value="<?php echo $radio['value']; ?>"
          class="radio"
          <?php if ($radio['value'] == $input) echo 'checked'; ?>
        /> 
        <?php echo $radio['label']; ?>
      </label>
    <?php endforeach; ?>
  </fieldset>
  
  <?php
}

// text
function default_text($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>">
    <?php echo $label; ?>
    <?php echo $error; ?>
    <input
      type="text"
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      value="<?php echo $input; ?>"
      class="text <?php if (isset($error)) echo 'valerie-alert'; ?>"
    />
  </label>
  
  <?php
}

// password
function default_password($args) {
  extract($args);
  ?>

  <label for="<?php echo $id; ?>">
    <?php echo $label; ?>
    <?php echo $error; ?>
    <input
      type="password"
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      value="<?php echo $input; ?>"
      class="password <?php if (isset($error)) echo 'valerie-alert'; ?>"
    />
  </label>

  <?php
}

// textarea
function default_textarea($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>">
    <?php echo $label; ?>
    <?php echo $error; ?>
    <textarea
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      <?php if (isset($error)) echo 'class="valerie-alert"'; ?>
    ><?php echo $input; ?></textarea>
  </label>
  
  <?php
}

// select
function default_select($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?><?php echo $error; ?>
    <select
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      <?php if (isset($error)) echo 'class="valerie-alert"'; ?>
      <?php if (isset($multiple)) echo "multiple=\"$multiple\""; ?>
      <?php if (isset($size)) echo "size=\"$size\""; ?>
    >
      <?php foreach($options as $option): ?>
        <option
          <?php if ($option['value'] == $input) echo 'selected'; ?>
          value="<?php echo $option['value']; ?>"
        >
          <?php echo $option['label']; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  
  <?php
}

// hidden
function default_hidden($args) {
  extract($args);
  ?>
  
  <input
    type="hidden"
    id="<?php echo $id; ?>"
    name="<?php echo $name; ?>"
    value="<?php echo $value; ?>"
    class="hidden"
  />
  
  <?php
}

// file
function default_file($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?><?php echo $error; ?>
    <input
      type="file"
      id="<?php echo $id; ?>"
      name="<?php echo $name; ?>"
      class="file"
    />
  </label>
  
  <?php
}

// button
function default_button($args) {
  extract($args);
  ?>
  
  <button
    type="<?php echo $button_type; ?>"
    name="<?php echo $name; ?>"
    value="<?php echo $value; ?>"
  >
    <?php echo $label; ?>
  </button>
  
  <?php
}

// submit
function default_submit($args) {
  extract($args);
  ?>
  
  <input type="submit" value="<?php echo $value; ?>" class="submit" />
  
  <?php
}

// fieldset
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
