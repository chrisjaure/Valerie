<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	defaults/plugin/template.php
//------------------------------------------------------------------------------

// form
function default_form($args) {
  extract($args);
  ?>
  
  <form
    <?php if (isset($id)) echo "id=\"$id\""; ?>
    class="valerie-form-default <?php echo $class; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      value="<?php echo $value; ?>"
      class="checkbox <?php echo $class; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      value="<?php echo $value; ?>"
      class="radio <?php echo $class; ?>"
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
  
  <fieldset class="<?php if (isset($error)) echo 'valerie-alert'; ?> <?php echo $class; ?>">
    <legend <?php if (isset($id)) echo "id=\"$id\""; ?>>
      <?php echo $label; ?>
      <?php echo $error; ?>
    </legend>
    <?php foreach($checkboxes as $checkbox): ?>
      <label for="<?php echo $checkbox['id']; ?>">
        <input
          type="checkbox"
          <?php if (isset($checkbox['id'])) echo "id=\"$checkbox[id]\""; ?>
          name="<?php echo $name; ?>"
          value="<?php echo $checkbox['value']; ?>"
          class="checkbox <?php echo $checkbox['class']; ?>"
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
  
  <fieldset class="<?php if (isset($error)) echo 'valerie-alert'; ?>  <?php echo $class; ?>">
    <legend <?php if (isset($id)) echo "id=\"$id\""; ?>>
      <?php echo $label; ?>
      <?php echo $error; ?>
    </legend>
    <?php foreach($radios as $radio): ?>
      <label for="<?php echo $radio['id']; ?>">
        <input
          type="radio"
          <?php if (isset($radio['id'])) echo "id=\"$radio[id]\""; ?>
          name="<?php echo $name; ?>"
          value="<?php echo $radio['value']; ?>"
          class="radio <?php echo $radio['class']; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      value="<?php echo $input; ?>"
      class="text <?php if (isset($error)) echo 'valerie-alert'; ?> <?php echo $class; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      value="<?php echo $input; ?>"
      class="password <?php if (isset($error)) echo 'valerie-alert'; ?> <?php echo $class; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      class="<?php if (isset($error)) echo 'valerie-alert'; ?> <?php echo $class?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      class="<?php if (isset($error)) echo 'valerie-alert'; ?> <?php echo $class; ?>"
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
    <?php if (isset($id)) echo "id=\"$id\""; ?>
    name="<?php echo $name; ?>"
    value="<?php echo $value; ?>"
    class="hidden <?php echo $class; ?>"
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
      <?php if (isset($id)) echo "id=\"$id\""; ?>
      name="<?php echo $name; ?>"
      class="file <?php echo $class; ?>"
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
    <?php if (isset($id)) echo "id=\"$id\""; ?>
    name="<?php echo $name; ?>"
    value="<?php echo $value; ?>"
    class="<?php echo $class; ?>"
  >
    <?php echo $label; ?>
  </button>
  
  <?php
}

// submit
function default_submit($args) {
  extract($args);
  ?>
  
  <input
    <?php if (isset($id)) echo "id=\"$id\""; ?>
    type="submit"
    value="<?php echo $label; ?>"
    class="submit <?php echo $class; ?>"
  />
  
  <?php
}

// fieldset
function default_fieldset($args) {
  extract($args);
  ?>
  
  <fieldset <?php if (isset($id)) echo "id=\"$id\""; ?> class="<?php echo $class; ?>">
    <legend><?php echo $label; ?></legend>
    <?php echo $content; ?>
  </fieldset>
  
  <?php
}

// link
function default_link($args) {
  extract($args);
  ?>
  
  <a
    <?php if (isset($id)) echo "id=\"$id\""; ?>
    href="<?php echo $href; ?>"
    class="<?php echo $class; ?>"
  >
    <?php echo $label; ?>
  </a>
  <?php
}
?>
