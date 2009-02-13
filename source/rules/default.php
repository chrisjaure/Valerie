<?php
$this->register(array(
  'required' => array('/^./', VAL_ERROR_REQUIRED),
  'int' => array('/^\d+$/', VAL_ERROR_INT),
  'alpha' => array('/^[a-z]+$/i', VAL_ERROR_ALPHA),
  'alphanumeric' => array('/^[a-z\d]+$/i', VAL_ERROR_ALPHANUMERIC),
  'currency' => array('/^(\$|\-|\$\-)?\d{1,3}([,]?\d{3})*(\.\d{2})?$/', VAL_ERROR_CURRENCY),
  'date' => array('#^(0[1-9]|1[012])[- /\.](0[1-9]|[12][0-9]|3[01])[- /\.](19|20)\d\d#', VAL_ERROR_DATE),
  'time' => array('/^([1-9]|0[1-9]|1[0-2]):[0-5]\d[\s]?(am|pm)$/i', VAL_ERROR_TIME),
  'time24' => array('/^([0-1]\d|2[0-3]):[0-5]\d$/', VAL_ERROR_TIME24),
  'phone' => array('/^[\(]?\d{3}[\)]?[\s|\.|-]?\d{3}[\s|\.|-]?\d{4}$/', VAL_ERROR_PHONE),
  'phoneintl' => array('/^\d{1,3}[\s|\.|-]\d{7,20}$/', VAL_ERROR_PHONEINTL),
  'postal' => array('/^([a-z]\d[a-z])[\s|-]?(\d[a-z]\d)$/i', VAL_ERROR_POSTAL),
  'zip' => array('/^\d{5}(-\d{4})?$/', VAL_ERROR_ZIP),
  'email' => array('/^([a-z0-9_-]+)(\.[a-z0-9_-]+)*@([a-z0-9_-]+)(\.[a-z0-9_-]+)*[\.]([a-z0-9_-]+)$/i', VAL_ERROR_EMAIL),
  'url' => array('/^((http|https|ftp):\/\/)?([a-z0-9_-]+)(\.[a-z0-9_-]+)+(\/\w+)*(\.[a-z0-9_-]+)*$/i', VAL_ERROR_URL),
  'ip' => array('/^(\d{1,3})(\.\d{1,3}){3}$/', VAL_ERROR_IP),
  'requiredif' =>array('requiredif', VAL_ERROR_REQUIREDIF),
  'confirm' => array('confirm', VAL_ERROR_CONFIRM),
  'differ' => array('differ', VAL_ERROR_DIFFER),
  'maxlength' => array('maxlength', VAL_ERROR_MAXLENGTH),
  'minlength' => array('minlength', VAL_ERROR_MINLENGTH)
));

function requiredif($val, $arg) {
  if (!$this->is_empty($this->get_value($arg))) {
    if ($this->is_empty($val)) return false;
  }
  return true;
}

function confirm($val, $args, $err) {
  list($name, $label) = $this->get_name_label($args);
  $message = $this->format($err, $label);
  return array($val == $this->get_value($name), $message);
}

function differ($val, $args, $err) {
  list($name, $label) = $this->get_name_label($args);
  $message = $this->format($err, $label);
  return array($val != $this->get_value($name), $message);
}

function maxlength($val, $length, $err) {
  $message = $this->format($err, $length);
  return array(strlen($val) <= (int) $length, $message);
}

function minlength($val, $length, $err) {
  $message = $this->format($err, $length);
  return array(strlen($val) >= (int) $length, $message);
}

?>
