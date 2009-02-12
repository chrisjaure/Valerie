<?php
$this->setTemplate(array(
  'form_message' => 'default_form_message',
  'field_error' => 'default_field_error',
  'checkbox' => 'default_checkbox',
  'form' => 'default_form',
  'submit' => 'default_submit',
  'text' => 'default_text',
  'fieldset' => 'default_fieldset'
));

function default_form_message($args) {
  extract($args);
  ?>
  
  <p class="<?php $type; ?>"><?php echo $message; ?></p>
  
  <?php
}

function default_field_error($args) {
  extract($args);
  ?>
  
  <span class="validator_error_wrapper">
    <span class="validator_error">
      <p class="content">
        <?php echo $message; ?>
      </p>
      <div class="bottom"></div>
    </span>
  </span>
  
  <?php
}

function default_checkbox($args) {
  extract($args);
  ?>
  
  <br />
  <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" /> 
  <label for="<?php echo $id; ?>" class="checkbox"><?php echo $label; ?></label>
  
  <?php
}

function default_form($args) {
  extract($args);
  ?>
  
  <form id="<?php echo $id; ?>" method="<?php echo $method; ?>" action="<?php echo $action; ?>">
    <?php echo $content; ?>
  </form>
  
  <?php
}

function default_submit($args) {
  extract($args);
  ?>
  
  <input type="submit" value="<?php echo $value; ?>" />
  
  <?php
}

function default_text($args) {
  extract($args);
  ?>
  
  <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
  <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
  
  <?php
}

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
