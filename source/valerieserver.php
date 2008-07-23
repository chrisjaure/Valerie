<?php
//------------------------------------------------------------------------------
//	Valerie v0.4
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	valerieserver.php
//------------------------------------------------------------------------------


class ValerieServer {
  
  var $values;
  var $rules;
  var $errors;
  var $ajax;
  var $periodical;
  var $patterns;

  function ValerieServer($data, $lang = 'en.php'){
    
    if (isset($data['_ajax'])) {
      $this->ajax = true;
      unset($data['_ajax']);
    } else {
      $this->ajax = false;
      session_start();
    }
    
    if (isset($data['_periodical'])) {
      $this->periodical = $data['_periodical'];
      unset($data['_periodical']);
    } else {
      $this->periodical = false;
    }
    
    require_once("localization/$lang"); 
    
    $this->patterns = array(
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
    );
    
    $this->rules = array();
    $this->values = array();
    foreach($data as $field => $value) {
    	list($name, $rules) = explode(':', $field, 2);
      $this->values[$name] = (!is_array($value)) ? trim($value) : $value;
      if (isset($rules)) {
        $this->rules[$name] = explode('|', $rules);
      }
    }
  }
  
  function validate(){
    
    if (!$this->ajax) unset($_SESSION['validator']);
    
    if ($this->periodical === false) {
      
      foreach($this->rules as $key => $rules) {
        $value = $this->values[$key];
        foreach($rules as $rule) {
           if(!$this->test($rule, $value, $key)) break;
        }
      }
      
      if (isset($this->errors)) {
        if ($this->ajax) {
          foreach ($this->errors as $key => $error) {
            $arr[] = '{"id": "' . $key. '", "message": "' . $error . '"}';
          }
          echo '{"type": 100, "content": [' . implode(', ', $arr) . '], "message": "' . VAL_INVALIDATE . '"}';
          die();
        } else {
          foreach($this->errors as $key => $error) {
              $_SESSION['validator'][$key . '_error'] = "<span class=\"error\">$error</span>";
          }
          foreach($this->values as $key => $value) {
            $_SESSION['validator'][$key] = $value;
          }
          $_SESSION['validator']['message'] = '<p class="error">' . VAL_INVALIDATE . '</p>';
          header("Location: {$_SESSION['referer']}");
          die();
        }
      } else {
        if ($this->ajax) {
          echo '{"type": 1, "content": {"message": "' . VAL_VALIDATE . '"}}';
        } else {
          $_SESSION['validator']['message'] = '<p class="success">' . VAL_VALIDATE . '</p>';
        }
      }
    } else {
    
      foreach($this->rules[$this->periodical] as $rule ) {
        if(!$this->test($rule, $this->values[$this->periodical], $this->periodical)) break;
      }
    
      if (isset($this->errors)) {
        echo '{"type": 100, "content": {"id": "' . key($this->errors) . '", "message": "' . current($this->errors) . '"}}';
      } else {
        echo '{"type": 1, "content": {"id": "'. $this->periodical . '"}}';
      }
      die();

    }
    
    return $this->values;
  }
  
  function register($patterns) {
    $this->patterns = array_merge($this->patterns, $patterns);
  }
  
  function back($bool = null) {
    if (!isset($bool)) $bool = $this->ajax;
    if (!$bool) header("Location: {$_SESSION['referer']}");
  }
  
  function is_ajax() {
    return $this->ajax;
  }
  
  function get_name_label($text) {
    if (is_array($text)) return array($text[0], str_replace('_', ' ', $text[1]));
    else return array($text, $text);
  }
  
  function get_value($id) {
    return $this->values[$id];
  }
  
  function get_rule($id) {
    return $this->rules[$id];
  }
  
  function is_empty($val) {
    return ($val == '' || $val == null);
  }
  
  function format($template, $values) {
    if (is_array($values)) {
      $replace = array();
      foreach(array_values($values) as $index => $value) {
        $replace[$index] = '{' . ($index + 1) . '}';
      }
    } else {
      $replace = '{1}';
    }
    return str_replace($replace, $values, $template);
  }
  
  function test($rule, $value, $name) {
    // match any arguments inside {}
    if (preg_match('/^(.*)\((.*)\)$/', $rule, $matches)) {
      $rule = $matches[1];
      $arguments = explode(',', $matches[2]);
      if (count($arguments) === 1) $arguments = $arguments[0];
    } else {
      $arguments = null;
    }

    // return true if empty and not required
    if ($this->is_empty($value) && $rule != 'required' && $rule != 'requiredif') return true;

    $error = $this->patterns[$rule][1];
    
    // check whether it's a class function, a custom function, or a regex pattern
    if (function_exists($this->patterns[$rule][0])) {
      $success = $this->patterns[$rule][0]($value, $arguments, $error);
    } elseif (method_exists($this, $rule)) {
      $success = $this->$rule($value, $arguments, $error);
    } else {
      $success = preg_match($this->patterns[$rule][0], $value);
    }
    
    if (is_array($success)) {
      $error = $success[1];
      $success = $success[0];
    }
    
    if (!$success) {
      $this->errors[$name] = htmlspecialchars(strip_tags($error));
      return false;
    } else return true;
    
  }
  
  function requiredif($val, $arg) {
    if (!$this->is_empty($this->values[$arg])) {
      if ($this->is_empty($val)) return false;
    }
    return true;
  }
  
  function confirm($val, $args, $err) {
    list($name, $label) = $this->get_name_label($args);
    $message = $this->format($err, $label);
    return array($val == $this->values[$name], $message);
  }
  
  function differ($val, $args, $err) {
    list($name, $label) = $this->get_name_label($args);
    $message = $this->format($err, $label);
    return array($val != $this->values[$name], $message);
  }
  
  function maxlength($val, $length, $err) {
    $message = $this->format($err, $length);
    return array(strlen($val) <= (int) $length, $message);
  }
  
  function minlength($val, $length, $err) {
    $message = $this->format($err, $length);
    return array(strlen($val) >= (int) $length, $message);
  }
  
}
?>
