<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerieserver.php
//------------------------------------------------------------------------------


/*
  Class: ValerieServer
  
  Validates the forms.
*/

class ValerieServer {
  
  private $values = array();
  private $rules = array();
  private $ids = array();
  private $errors;
  private $ajax;
  private $periodical;
  private $patterns = array();
  private $referer;
  private $definition;
  private $uid;

  /*
    Contructor: __construct
    
    Arguments:
    
      $data - POST data
  */

  public function __construct($data, $lang = 'en.php'){
    
    @session_start();
    
    $this->ajax = isset($data['_ajax']);
    $this->periodical = isset($data['_periodical']);
    
    $this->uid = $data['formid'];
    $this->referer = $_SESSION['validator']['referer'];
    $this->definition = unserialize($_SESSION['validator'][$this->uid]);
    
    if (!is_array($this->definition)) {
      if ($this->ajax) {
        exit ('Could not find form definition.');
      }
      else {
        $_SESSION['validator']['message'] = "An error has occured.";
        $_SESSION['validator']['message_type'] = 'error';
        $this->back();
      }
    }
    
    require_once("localization/$lang"); 
    
    $this->setValues($this->definition['elements'], $data);
    
  }
  
  /*
    Method: setValues
    
    Matches elements from the form definition with the submitted values.
    Recursive.
    
    Arguments:
    
      $els - array of form elements
      $val - array of submitted values.
  */
  
  private function setValues($els, $vals) {
    foreach($els as $element) {
      if (isset($element['elements'])) $this->setValues($element['elements'], $vals);
      if (isset($element['name'])) {
        $name = $element['name'];
        $post_key = $name;
        if (substr($name, -2) == '[]') $post_key = substr($name, 0, -2);
        $this->values[$name] = (!is_array($vals[$post_key])) ? trim($vals[$post_key]) : $vals[$post_key];
        if (isset($element['validation'])) {
          $this->ids[$name] = $element['id'];
          $this->rules[$name] = (is_array($element['validation'])) ? $element['validation'] : array($element['validation']);
        }
      }
    }
  }
  
  /*
    Method: validate
    
    Validates the submitted form data. If the form has been submitted via ajax,
    messages will be echoed, otherwise they are stored in session variables.
    
    Returns:
    
      If the form validates, the values will be returned.
  */
  
  public function validate(){
    
    if (!$this->ajax) unset($_SESSION['validator']);
    
    if ($this->periodical === false) {
      
      foreach($this->rules as $key => $rules) {
        $value = $this->values[$key];
        if (!is_array($value)) $value = array($value);
        foreach ($value as $val) {
          foreach($rules as $rule) {
            if(!$this->test($rule, $val, $key)) break;
          }
        }
      }
      
      if (isset($this->errors)) {
        if ($this->ajax) {
          foreach ($this->errors as $key => $error) {
            $arr[] = '{"id": "' . $this->ids[$key] . '", "message": "' . $error . '"}';
          }
          echo '{"type": 100, "content": [' . implode(', ', $arr) . '], "message": "' . VAL_INVALIDATE . '"}';
          exit();
        } else {
          foreach($this->errors as $key => $error) {
            $_SESSION['validator'][$key . '_error'] = $error;
          }
          foreach($this->values as $key => $value) {
            echo $key, ': ', var_dump($value), "<br>";
            $_SESSION['validator'][$key] = $value;
          }
          $_SESSION['validator']['message'] = VAL_INVALIDATE;
          $_SESSION['validator']['message_type'] = 'error';
          $this->back();
        }
      } else {
        if ($this->ajax) {
          echo '{"type": 1, "message": "' . VAL_VALIDATE . '"}';
        } else {
          $_SESSION['validator']['message'] = VAL_VALIDATE;
          $_SESSION['validator']['message_type'] = 'success';
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
      exit();

    }
    
    return $this->values;
  }
  
  /*
    Method: register
    
    Registers a pattern to validate values against.
    
    Arguments:
    
      $patterns - array of key/value pairs. The value must also be an array
      containing a regex or function and the message to send if the value is
      invalid.
      
    Example:
    
      $form->register(array(
        'required' => array('/^./', 'Field {1} is required.'),
      ));
  */
  
  public function register($patterns) {
    $this->patterns = array_merge($this->patterns, $patterns);
  }
  
  /*
    Method: back
    
    Sends the browser back to the form after submission if javascript is
    disabled.
  */
  
  public function back($bool = null) {
    if (!isset($bool)) $bool = $this->ajax;
    if (!$bool) {
        header("Location: {$this->referer}");
        exit();
    }
  }
  
  /*
    Method: isAjax
    
    
  */
  
  public function isAjax() {
    return $this->ajax;
  }
  
  /*
    Method: getNameLabel
    
    
  */
  
  public function getNameLabel($text) {
    if (is_array($text)) return array($text[0], $text[1]);
    else return array($text, $text);
  }
  
  /*
    Method: getValue
    
    
  */
  
  public function getValue($id) {
    return $this->values[$id];
  }
  
  /*
    Method: getRule
    
    
  */
  
  public function getRule($id) {
    return $this->rules[$id];
  }
  
  /*
    Method: isEmpty
    
    
  */
  
  public function isEmpty($val) {
    return ($val == '' || $val == null);
  }
  
  /*
    Method: format
    
    
  */
  
  public function format($template, $values) {
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
  
  /*
    Method: test
    
    
  */
  
  private function test($rule, $value, $name) {
    //get rule arguments
    if (is_array($rule)) {
      $arguments = $rule;
      list($rule) = array_keys($rule);
      $arguments = $arguments[$rule];
      if (count($arguments) === 1) $arguments = $arguments[0];
    }
    else {
      $arguments = null;
    }

    //var_dump($rule, $arguments);

    // return true if empty and not required
    if ($this->isEmpty($value) && !$this->patterns[$rule][2]) return true;

    $error = $this->patterns[$rule][1];
    
    // check whether it's a function or a regex pattern
    if (function_exists($this->patterns[$rule][0])) {
      $success = $this->patterns[$rule][0](array(
        "name" => $name,
        "value" => $value,
        "arguments" => $arguments,
        "error" => $error
      ), $this);
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
}
?>
