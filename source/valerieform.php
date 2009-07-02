<?php
//------------------------------------------------------------------------------
//	Valerie
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerieform.php
//------------------------------------------------------------------------------

/*
  Class: ValerieForm

  Contains methods for creating form display.
*/

class ValerieForm {
  private $template = array();
  private $definition = array();
  private $output;
  private $replace;
  private $formats = array();
  private $root;
  private $uri = array();
  private $plugin;
  private $includes = array();
  private $uid;
  private $ns;
  
  /*
    Constructor: __construct
    
    Creates instance of ValerieForm.
    
    Arguments:
    
      - $plugin - name of plugin folder
      
    Returns:
    
      - instance of ValerieForm
  */
  
  public function __construct($plugin = 'default') {
    @session_start();
    $this->root = App::get('config:root');
    $this->uri = array(
      'source' => App::get('config:source_uri'),
      'plugin' => App::get('config:plugin_uri') . $plugin
    );
    $this->uid = md5(rand().time());
    $this->plugin = $plugin;
    $config = App::get("styles:$plugin");
    if (isset($config['uri'])) {
      $this->uri['plugin'] = $config['uri'];
    }
    $this->template = $config['templates'];
    $this->includes = $config['includes'];
    if(!isset($this->template)) {
      trigger_error("Form template could not be set using style '$plugin'", E_USER_ERROR);
    }
  }
  
  public function __destruct() {
    if (isset($this->ns)) {
      unset($_SESSION[$this->ns]);
      $_SESSION[$this->ns]['referer'] = $_SERVER['PHP_SELF'];
      $_SESSION[App::get('config:session_ns')][$this->uid] = serialize(
        $this->definition
      );
    }
  }
  
  /*
    Method: setTemplate
    
    Set html markup to be used by renderer.
    
    Arguments:
    
      - $form - array of key value pairs
      
    Example:
    
        $form->setTemplate(array(
          'checkbox': 'checkbox_format'
        ));
        
        function checkbox_format($args) {
          extract($args) ?>
          
          <input
            type="checkbox"
            id="<?php echo $id; ?>"
            name="<?php echo $name; ?>"
            value="<?php echo $value; ?>"
          /> 
          <label for="<?php echo $id; ?>" class="checkbox">
            <?php echo $label; ?>
          </label>
          <?php echo $error; ?>
          
          <?php
        }
  */
  
  public function setTemplate($form) {
    if (is_array($form)) {
      $this->template = array_merge($this->template, $form);
    }
  }
  
  /*
    Method: setDefinition
    
    Sets the form structure (fields, etc). Relative to /forms directory.
    
    Definition can be an array or JSON object. Nested elements shoud always
    be contained in key 'elements' unless the template function handles it.
    Any keys set in definition will be available to template functions.
    The template function to be used is determined by first checking for 'id',
    then 'type'.
    
    Example:
    
        //element definition
        {
          "type": "text",
          "id": "text1",
          "name": "text1",
          "label": "This is a text field",
          "validation": [
            "required",
            {
              "maxlength": [25]
            }
          ]
        }
        
        //setting definition
        $Valerie->setDefinition(array(
          "text" => "text_func",
          "text1" => "special_func_for_text1"
        ));
        
        function text_func($args) {...}
        function special_func_for_text1($args) {...}
        
    In this example, special_func_for_text1 will be used to render the
    defined text field because it has been registered for that element's id.
    
    All template functions receive an array of the element's definition.
    Please note the reserved keys 'error', 'input', and 'selected'. These
    are used when javascript is disabled.
    
    'error' contains the error message if the field is invalid. 'input'
    contains any user input. 'selected' contains a boolean value indicating
    if a radio, checkbox, or option is selected.
  */
  
  public function setDefinition($definition) {
    if (is_array($definition)) {
      $this->definition = array_merge_recursive($this->definition, $definition);
    }
    else {
      $this->definition = array_merge_recursive(
        $this->definition,
        $this->getArrayFromJSON($definition)
      );
    }
    $this->ns = App::get('config:session_ns') .
      $this->definition['attributes']['id'];
    App::set('form_id', $this->definition['attributes']['id']);
  }
  
  /*
    Method: setIncludes
    
    Set a list of css/js files to include for plugin use. Note that
    'style.css' and 'script.js' are automatically included.
    
    The includes should be defined in an array with type/path pairs.
    To wrap the asset in an IE conditional comment, define an array
    containing the condition and the path.
    
    Arguments:
      
      - $includes - associative array with type/relative path pairs.
    
    Example:
    
        $Valerie->setIncludes(array(
          'css' => 'style2.css',
          'css' => array('lt IE 7', 'ie6_style.css'),
          'js' => 'script2.js'
        ))
  */
  
  public function setIncludes($includes) {
    $this->includes = array_merge($this->includes, $includes);
  }
  
  /*
    Method: getArrayFromJSON
    
    Reads a JSON text file and returns an associative array.
    
    Arguments:
    
      - $file - filepath or filename relative to root
      
    Returns:
    
      - array
  */
  
  private function getArrayFromJSON($file) {
    if (file_exists($file)) {
      $path = $file;
    }
    elseif (file_exists(dirname($_SERVER['PHP_SELF'] . $file))) {
      $path = dirname($_SERVER['PHP_SELF'] . $file);
    }
    elseif (file_exists($this->root . $file)) {
      $path = $this->root . $file;
    }
    return json_decode(@file_get_contents($path), true);
  }
  
  /*
    Method: getOutput
    
    Generates form mark-up. Recursive.
    
    Arguments:
    
      - $data - json definition
    
    Returns:
    
      - string
  */
  
  private function getOutput($data) {
    $output = '';
    if (!is_array($data)) {
      trigger_error("Form definition is not structured correctly", E_USER_ERROR);
    }
    foreach($data as $args) {
      if ($this->template[$args['id']]) {
        $fn = $this->template[$args['id']];
      }
      else {
        $fn = $this->template[$args['type']];
      }
      $input = $this->getValue($args['name']);
      $vals = $args + array(
        'error' => $this->getError($args),
        'input' => $input,
        'selected' => (is_array($input)) ? 
          in_array($args['value'], $input) :
          $args['value'] == $input
      );
      if (isset($args['elements'])) {
        $vals = $vals + array('content' => $this->getOutput($args['elements']));
      }
      $id = ucfirst($args['id']);
      $type = ucfirst($args['type']);
      ob_start();
      
      Valerie::fireHooks('beforePrintField', array(&$vals));
      Valerie::fireHooks("beforePrint$id", array(&$vals));
      Valerie::fireHooks("beforePrint$type", array(&$vals));
      
      call_user_func($fn, $vals);
      
      Valerie::fireHooks('afterPrintField', array(&$vals));
      Valerie::fireHooks("afterPrint$id", array(&$vals));
      Valerie::fireHooks("afterPrint$type", array(&$vals));
      
      $output .= ob_get_contents();
      ob_end_clean();
    }
    return $output;
  }
  
  /*
    Method: render
    
    Prints the form mark-up. Includes css and javascript files.
  */
  
  public function render() {
    Valerie::fireHooks('beforePrintForm', array(&$this));
    
    $output = '<input type="hidden" name="formid" value="'.$this->uid.'" />';
    $output .= $this->getOutput($this->definition['elements']);
    
    echo "<script type=\"text/javascript\">" .
      "jQuery(function($){ $(\"#{$this->definition['attributes']['id']}\")" .
      ".valerie(); })</script>\n";
    
    echo call_user_func($this->template['form'], $this->definition['attributes'] + array(
      'content' => $output,
      'message' => $this->getMessage()
    ));
    
    Valerie::fireHooks('afterPrintForm', array(&$this, &$output));
  }
  
  /*
    Method: getResponse
  
    Get a response set by ValerieServer
    
    Arguments:
    
      - $name - index of response
      - $namespace - namespace response was saved in
      
    Returns:
    
      - response
  */
  
  public function getResponse($name, $namespace = null) {
    if (strpos($name, ':') !== false) {
      list($namespace, $name) = explode(':', $name, 2);
    }
    if (isset($namespace)) {
      return $_SESSION[$this->ns][$namespace][$name];
    }
    else {
      return $_SESSION[$this->ns][$name];
    }
  }
    
  /*
    Method: getMessageType
    
    Returns the status of the message. Used only when javascript isn't enabled.
    
    Returns:
    
      - string 'error' or 'success'
    
  */
  
  public function getMessageType() {
    return $_SESSION[$this->ns]['form']['message_type'];
  }
  
  /*
    Method: printMessageType
    
    Prints status of the message.
    
    See Also:
    
      - < getMessageType >
  */
  
  public function printMessageType() {
    echo $this->getMessageType();
  }
  
  /*
    Method: getMessage
    
    Gets the message created after the form has been submitted and validated.
    
    Returns:
    
      - Form validation message.
  */
  
  public function getMessage() {
    $message = $_SESSION[$this->ns]['form']['message'];
    if (isset($message)) {
      if (function_exists($this->template['form_message'])) {
        ob_start();
        $this->template['form_message'](array(
          'message' => $message,
          'type' => $this->getMessageType()
        ));
        $message = ob_get_contents();
        ob_end_clean();
      }
      return $message;
    }
    else {
      return null;
    }
  }
  
  /*
    Method: printMessage
    
    Prints form message.
    
    See Also:
    
      - < getMessage >
  */
  
  public function printMessage() {
    echo $this->getMessage();
  }
  
  /*
    Method: getValue
    
    Gets the value of a submitted field.
    
    Arguments:
    
      - $name - name of field
    
    Returns:
    
      - Value of a submitted field.
  */
  
  public function getValue($name) {
    if (substr($name, -2) == '[]') {
      $name = substr($name, 0, -2);
    }
    $value = $this->getResponse('elements', 'form');
    return $value[$name]['value']; 
  }
  
  /*
    Method: printValue
    
    Arguments:
    
      - $name - name of field
    
    See Also:
    
      - < getValue >
  */
  
  public function printValue($name) {
    echo $this->getValue($name);
  }
  
  /*
    Method: getError
    
    Get the validation error of a field after the form has been submitted.
    
    Arguments:
    
      - $args - array, field arguments
    
    Returns:
    
      - Field validation error or null.
  */
  
  public function getError($args) {
    $name = $args['name'];
    if (substr($name, -2) == '[]') $name = substr($name, 0, -2);
    $error = $this->getResponse('elements', 'form');
    $error = $error[$name]['message'];
    if ($error) {
      if (is_callable($this->template['field_error'])) {
        ob_start();
        call_user_func($this->template['field_error'], $args + array('message' => $error));
        $error = ob_get_contents();
        ob_end_clean();
      }
      return $error;
    }
    else {
      return null;
    }
  }
  
  /*
    Method: printError
    
    Arguments:
    
      - $args - array, field arguments
    
    See Also:
    
      - < getError >
  */
  
  public function printError($args) {
    $this->getError($args);
  }
  
  /*
    Method: printAssets
    
    Prints necessary css/javascript files.
    
    Arguments:
    
      - $global - bool, print all required files or only those for the plugin
  */
  
  public function printAssets($global = true) {
    echo "\n\n<!-- Begin JibberBook {$this->plugin} Assets -->\n";
    Valerie::fireHooks('beforePrintAssets');
    if ($global) {
      echo "<script type=\"text/javascript\" src=" .
        "\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\">" .
        "</script>\n";
      echo "<script type=\"text/javascript\" " .
        "src=\"{$this->uri['source']}valerieclient.js\"></script>\n";
    }
    echo "<link rel=\"stylesheet\" type=\"text/css\" ".
      "href=\"{$this->uri['plugin']}/style.css\" />\n";
    echo "<script type=\"text/javascript\" " .
      "src=\"{$this->uri['plugin']}/script.js\"></script>\n";
    
    foreach ((array) $this->includes as $type => $path) {
      if (is_array($path)) {
        $conditional = $path[0];
        $path = $path[1];
        echo "<!--[if $conditional]>\n";
      }
      $path = strip_tags($path);
      switch ($type) {
        case 'css':
          echo "<link rel=\"stylesheet\" type=\"text/css\" ".
            "href=\"{$this->uri['plugin']}/$path\" />\n";
          break;
        case 'js':
          echo "<script type=\"text/javascript\" ".
            "src=\"{$this->uri['plugin']}/$path\"></script>\n";
          break;
      }
      if (isset($conditional)) {
        echo "<![endif]-->\n";
      }
    }
    Valerie::fireHooks('afterPrintAssets');
    echo "<!-- End JibberBook {$this->plugin} Assets -->\n\n";
  }
}
?>
