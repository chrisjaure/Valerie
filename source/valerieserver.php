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
  
  Validates the forms. Any form values are converted to UTF-8 charset.
*/

class ValerieServer {
  
  private $values = array();
  private $rules = array();
  private $elements = array();
  private $errors;
  private $ajax;
  private $periodical;
  private $patterns = array();
  private $filters = array();
  private $referer;
  private $definition;
  private $uid;
  private $response = array();
  private $responseReturned;

  /*
    Constructor: __construct
    
    Arguments:
    
      - $data - POST data
      - $lang - language file to use, filename minus .php
      
    Returns:
    
      - ValerieServer instance
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
    
    require_once "localization/$lang";
    require_once "libs/utf8/utf8.php";
    
    $this->setValues($this->definition['elements'], $data);
  }
  
  /*
    Method: setValues
    
    Matches elements from the form definition with the submitted values.
    Recursive.
    
    Arguments:
    
      - $els - array of form elements
      - $val - array of submitted values.
  */
  
  private function setValues($els, $vals) {
    foreach($els as $element) {
      if (isset($element['elements']))
        $this->setValues($element['elements'], $vals);
      if (isset($element['name'])) {
        $name = $element['name'];
        if (substr($name, -2) == '[]') $name = substr($name, 0, -2);
        if (is_array($vals[$name])) {
          foreach ($vals[$name] as &$value) {
            $value = $this->cleanValue($value);
          }
          $this->values[$name] = $vals[$name];
        }
        else {
          $this->values[$name] = $this->cleanValue($vals[$name]);
        }
        if (isset($element['validation'])) {
          if (is_array($element['validation']))
            $this->rules[$name] = $element['validation'];
          else
            $this->rules[$name] = array($element['validation']);
        }
        $this->elements[$name] = $element;
      }
    }
  }
  
  /*
    Method: cleanValue
    
    This will trim the value and make sure it's a valid UTF-8 string.
  */
  
  private function cleanValue($value) {
    $value = trim($value);
    if (strtoupper(ValerieConfig::CHAR_ENCODING) != 'UTF-8') {
      $value = iconv(ValerieConfig::CHAR_ENCODING, 'UTF-8//TRANSLIT', $value);
    }
    else {
      $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }
    
    return $value;
  }
  
  /*
    Method: validate
    
    Validates the submitted form data.
    
    Returns:
    
      - array of values if the form validates, otherwise false.
  */
  
  public function validate() {
    
    if (!$this->ajax) unset($_SESSION['validator']);
    
    if ($this->periodical === false) {
      
      // validate
      foreach($this->rules as $name => $rules) {
        $values = $this->values[$name];
        if (!is_array($values)) $values = array($values);
        foreach ($values as $value) {
          foreach($rules as $rule) {
            if(!$this->test($rule, $value, $name)) break;
          }
        }
      }
      
      // return any errors
      if (isset($this->errors)) {
        $invalidated = __('Please correct the errors below.');
        if ($this->ajax) {
          $elements = array();
          foreach ($this->errors as $key => $error) {
            $elements[] = array(
              'id' => $this->elements[$key]['id'],
              'message' => $error
            );
          }
          $this->setResponse(array(
            'message_type' => 'invalid',
            'message' => $invalidated,
            'elements' => $elements
          ));
        } else {
          foreach($this->errors as $key => $error) {
            $this->setResponse($key.'_error', $error);
          }
          foreach($this->values as $key => $value) {
            $this->setResponse($key, $value);
          }
          $this->setResponse(array(
            'message' => $invalidated,
            'message_type' => 'invalid'
          ));
        }
      } else {
        $this->setResponse(array(
          'message_type' => 'valid',
          'message' => __('Your form has been submitted.')
        ));
      }
    } else {
    
      foreach($this->rules[$this->periodical] as $rule ) {
        if(!$this->test(
          $rule,
          $this->values[$this->periodical],
          $this->periodical
        )) break;
      }
    
      if (isset($this->errors)) {
        echo '{"type": "invalid", "content": {"id": "' . key($this->errors) .
          '", "message": "' . current($this->errors) . '"}}';
      } else {
        echo '{"type": "valid", "content": {"id": "'. $this->periodical . '"}}';
      }
      exit();

    }
    
    if (!isset($this->errors)) {
    
      // filter
      foreach ($this->elements as $name => $element) {
        $filters = $element['filters'];
        if (isset($filters)) {
          if (!is_array($filters)) $filters = array($filters);
          foreach ($filters as $filter) {
            $this->values[$name] = $this->filter($filter, $this->values[$name]);
          }
        }
      }
      
      return $this->values;
    }
    else return false;
  }
  
  /*
    Method: register
    
    Registers a pattern to validate values against.
    
    Arguments:
    
      - $patterns - array of key/value pairs. The key is to be used in the form
      definition. The value must also be an array containing a regex or function
      and the message to send if the value is invalid.
      
    Example:
    
        $form->registerRules(array(
          'required' => array('/^./', 'This field is required.')
        ));
  */
  
  public function registerRules($patterns) {
    $this->patterns = array_merge($this->patterns, $patterns);
  }
  
  /*
    Method: registerFilters
    
    Registers filters to be applied to values AFTER they have been validated.
    
    Arguments:
    
      - $filters - array of key/value pairs.
      
    Example:
    
        $form->registerFilters(array(
          'striptags' => 'strip_tags'
        ));
  */
  
  public function registerFilters($filters) {
    $this->filters = array_merge($this->filters, $filters);
  }
  
  /*
    Method: setResponse
    
    Set a value to be sent back to form.
  */
  
  public function setResponse($name, $value = null) {
    if ($this->ajax) {
      if (is_array($name)) {
        foreach($name as $key => $value) {
          $this->response[$key] = $value;
        }
      }
      else {
        $this->response[$name] = $value;
      }
    }
    else {
      if (is_array($name)) {
        foreach($name as $key => $value) {
          $_SESSION['validator'][$key] = $value;
        }
      }
      else {
        $_SESSION['validator'][$name] = $value;
      }
    }
  }
  
  /*
    Method: printResponse
    
    Sends json back to client if ajax request.
  */
  
  public function printResponse() {
    if ($this->ajax) {
      echo json_encode($this->response);
    }
  }
  
  /*
    Method: back
    
    Sends the browser back to the form after submission if javascript is
    disabled.
  */
  
  public function back() {
    if (!$this->ajax) {
      header("Location: {$this->referer}");
      exit();
    }
    $this->printResponse();
  }
  
  /*
    Method: goto
    
    Sends the browser to the provided url.
    
    Arguments:
    
      - $url - url to redirect to
  */
  
  public function goto($url) {
    if ($this->ajax) {
      $this->setResponse('goto', $url);
    }
    else {
      header("Location: $url");
      exit();
    }
    $this->printResponse();
  }
  
  /*
    Method: isAjax
    
    Returns:
    
      - bool, true if ajax request
  */
  
  public function isAjax() {
    return $this->ajax;
  }
  
  /*
    Method: getNameLabel
    
    Returns a two-value array. If an array is passed in, first two values are
    returned. Otherwise the array is populated with the string.
    
    Arguments:
      
      - $text - string or array
      
    Returns:
    
      - array
  */
  
  public function getNameLabel($text) {
    if (is_array($text)) return array($text[0], $text[1]);
    else return array($text, $text);
  }
  
  /*
    Method: getValue
    
    Gets the form value(s).
    
    Arguments:
      
      - $name - name of field
      
    Returns:
    
      - string or array
  */
  
  public function getValue($name) {
    return $this->values[$name];
  }
  
  /*
    Method: getRule
    
    Gets the rule(s) associated with form field.
    
    Arguments:
    
      - $name - name of field
      
    Returns:
    
      - string or array
  */
  
  public function getRule($name) {
    return $this->rules[$name];
  }
  
  /*
    Method: isEmpty
    
    Determines if field value is empty.
    
    Arguments:
    
      - $val - value
      
    Returns:
    
      - bool
  */
  
  public function isEmpty($val) {
    return ($val == '' || $val == null);
  }
  
  /*
    Method: format
    
    Replaces n tokens ({1}, {2}, ...) with values.
    
    Arguments:
    
      - $template - string with tokens to be replaced
      - $values - string value or array of values
      
    Returns:
    
      - string
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
    
    Checks value against its validation rules.
    
    Arguments:
    
      - $rule - rule name
      - $value - form value
      - $name - form value name
      
    Returns:
    
      - bool, true on success, false on failure
  */
  
  private function test($rule, $value, $name) {
    //get rule arguments
    if (is_array($rule)) {
      $arguments = $rule;
      list($rule) = array_keys($rule);
      $arguments = $arguments[$rule];
    }
    else {
      $arguments = null;
    }

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
  
  /*
    Method: filter
    
    Filters values.
    
    Arguments:
    
      - $filter - filter name
      - $values - value(s) to filter
      
    Returns:
    
      - string or array
      
  */
  
  private function filter($filter, $values) {
    // get arguments
    if (is_array($filter)) {
      $arguments = $filter;
      list($filter) = array_keys($filter);
      $arguments = $arguments[$filter];
    }
    else {
      $arguments = null;
    }
  
    $fn = $this->filters[$filter];
    if (is_array($values)) {
      foreach($values as &$value) {
        if (function_exists($fn)) {
          $value = $fn($value, $arguments);
        }
      }
    }
    else {
      if (function_exists($fn)) {
        $values = $fn($values, $arguments);
      }
    }
    return $values;
  }
}
?>
