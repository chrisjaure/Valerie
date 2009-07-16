<?php
//------------------------------------------------------------------------------
//	Valerie
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
  private $ns;

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
    $this->definition = unserialize(
      $_SESSION[App::get('config:session_ns')][$this->uid]
    );
    
    if (!is_array($this->definition)) {
      $error = 'Form definition could not be found at this location: $_SESSION['
        . App::get('config:session_ns') . '][' . $this->uid . ']';
      trigger_error($error, E_USER_ERROR);
    }
    
    require_once "localization/$lang";
    require_once "libs/utf8/utf8.php";

    $this->ns = App::get('config:session_ns') .
      $this->definition['attributes']['id'];
    $this->referer = $_SESSION[$this->ns]['referer'];
    $this->setValues($this->definition['elements'], $data);
    
    $this->filters = App::get('filters');
    $this->patterns = App::get('rules');
    
    App::set('form_id', $this->definition['attributes']['id']);
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
      if (isset($element['elements'])) {
        $this->setValues($element['elements'], $vals);
      }
      if (isset($element['name'])) {
        $name = $element['name'];
        if (substr($name, -2) == '[]') {
          $name = substr($name, 0, -2);
        }
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
          $this->rules[$name] = (array) $element['validation'];
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
    if (strtoupper(App::get('config:char_encoding')) != 'UTF-8') {
      $value = iconv(
        App::get('config:char_encoding'),
        'UTF-8//TRANSLIT',
        $value
      );
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
    Valerie::fireHooks('beforeValidateForm', array(&$this));
    
    if (!$this->ajax) unset($_SESSION[$this->ns]);
    
    if ($this->periodical === false) {
      
      // validate
      foreach($this->rules as $name => $rules) {
        $values = $this->values[$name];
        foreach ((array) $values as $value) {
          foreach($rules as $rule) {
            if(!$this->test($rule, $value, $name)) break;
          }
        }
      }
      
      // return any errors
      if (isset($this->errors)) {
        $elements = array();
        foreach ($this->elements as $name => $element) {
          $el = array(
            'id' => $element['id'],
            'name' => $name,
            'message' => $this->errors[$name]
          );
          if (!$this->ajax) {
            $el['value'] = $this->values[$name];
          }
          $elements[$name] = $el;
        }
        $this->setResponse(array(
          'message_type' => 'invalid',
          'message' => __('Please correct the errors below.'),
          'elements' => $elements
        ), 'form');
      }
      else {
        $this->setResponse(array(
          'message_type' => 'valid',
          'message' => __('Your form has been submitted.')
        ), 'form');
      }
    }
    else {
    
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
      }
      else {
        echo '{"type": "valid", "content": {"id": "'. $this->periodical . '"}}';
      }
      exit();

    }
    
    Valerie::fireHooks('afterValidateForm', array(&$this, &$this->values, (!isset($this->errors))));
    
    if (!isset($this->errors)) {
    
      // filter
      foreach ($this->elements as $name => $element) {
        $filters = $element['filters'];
        if (isset($filters)) {
          foreach ((array) $filters as $filter) {
            $this->values[$name] = $this->filter($filter, $this->values[$name]);
          }
        }
      }
      Valerie::fireHooks('onFormValidated', array(&$this, &$this->values));
      return $this->values;
    }
    else {
      Valerie::fireHooks('onFormInvalidated', array(&$this, &$this->values));
      return false;
    }
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
    
    Arguments:
      
      - $name - array of name/value pairs or string name
      - $value - string value if $name is string
      - $namespace - if this is set, values will be saved in an array with this
      index
  */
  
  public function setResponse($name, $value = null, $namespace = null) {
    if (!is_array($name)) {
      $name = array($name => $value);
    }
    else {
      $namespace = $value;
    }
    if ($this->ajax) {
      $container = &$this->response;
      if (isset($namespace)) {
        $container = &$this->response[$namespace];
      }
    }
    else {
      $container = &$_SESSION[$this->ns];
      if (isset($namespace)) {
        $container = &$_SESSION[$this->ns][$namespace];
      }
    }
    if (!is_array($container)) {
      $container = array();
    }
    foreach($name as $key => $value) {
      if (strpos($key, ':') !== false) {
        list($namespace, $key) = explode(':', $key, 2);
        $container[$namespace][$key] = $value;
      }
      else {
        $container[$key] = $value;
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
    exit();
  }
  
  /*
    Method: back
    
    Sends the browser back to the form after submission if javascript is
    disabled.
  */
  
  public function back($message = null, $type = null) {
    if ($message) {
      $this->setResponse('form:message', $message);
    }
    if ($type) {
      $this->setResponse('form:message_type', $type);
    }
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
    Valerie::fireHooks('beforeRedirect', array(&$this));
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
    if (is_array($text)) {
      return array($text[0], $text[1]);
    }
    else {
      return array($text, $text);
    }
  }
  
  /*
    Method: getFormId
    
    Returns the form id
  */
  
  public function getFormId() {
    return $this->definition['attributes']['id'];
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
    }
    else {
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
    if ($this->isEmpty($value) && !$this->patterns[$rule][2]) {
      return true;
    }

    $error = $this->patterns[$rule][1];
    
    // check whether it's a function or a regex pattern
    if (is_callable($this->patterns[$rule][0])) {
      $success = call_user_func($this->patterns[$rule][0], array(
        "name" => $name,
        "value" => $value,
        "arguments" => $arguments,
        "error" => $error
      ), $this);
    }
    else {
      $success = preg_match($this->patterns[$rule][0], $value);
    }
    
    if (is_array($success)) {
      $error = $success[1];
      $success = $success[0];
    }
    
    if (!$success) {
      $this->errors[$name] = htmlspecialchars(strip_tags($error));
      return false;
    }
    else return true;
    
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
    $values = (array) $values;
    foreach ($values as &$value) {
      call_user_func($fn, $value, $arguments);
    }
    return $values;
  }
}
?>
