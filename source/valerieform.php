<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
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
  private $definition;
  private $output;
  private $replace;
  private $formats = array();
  private $root;
  private $uri;
  private $plugin;
  private $includes = array();
  private $uid;
  
  /*
    Constructor: __construct
    
    Creates instance of ValerieForm.
    
    Arguments:
    
      $plugin - name of plugin folder
      
    Returns:
    
      instance of ValerieForm
  */
  
  public function __construct($plugin) {
    @session_start();
    if (ValerieConfig::ROOT) {
      $this->root = ValerieConfig::ROOT;
    }
    else {
      $this->root = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . '/';
    }
    if (ValerieConfig::URI) {
      $this->uri = ValerieConfig::URI;
    }
    else {
      $this->uri = '../source/';
    }
    $this->uid = md5(rand().time());
    $this->plugin = $plugin;
    $this->setDefinition('forms/default_form.php');
  }
  
  public function __destruct() {
    session_unset($_SESSION['validator']);
    $_SESSION['validator']['referer'] = $_SERVER['PHP_SELF'];
    $_SESSION['validator'][$this->uid] = serialize($this->definition);
  }
  
  /*
    Method: setTemplate
    
    Set html markup to be used by renderer.
    
    Arguments:
    
      $form - array of key value pairs
      
    Example:
    
        $form->setTemplate(array(
          'checkbox': 'checkbox_format'
        ));
        
        function checkbox_format($args) {
          extract($args) ?>
          
          <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" /> 
          <label for="<?php echo $id; ?>" class="checkbox"><?php echo $label; ?></label>
          <?php echo $error; ?>
          
          <?php
        }
  */
  
  public function setTemplate($form) {
    if (is_array($form)) $this->template = array_merge($this->template, $form);
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
    is a radio or checkbox is selected.
  */
  
  public function setDefinition($definition) {
    if (is_array($definition)) $this->definition = $definition;
    else {
      $this->definition = $this->getArrayFromJSON($definition);
    }
  }
  
  /*
    Method: setIncludes
    
    Set a list of css/js files to include for plugin use. Note that
    'style.css' and 'script.js' are automatically included.
    The includes should be defined in an array with type/path pairs.
    To wrap the asset in an IE conditional comment, define an array
    containing the condition and the path.
    
    Arguments:
      
      $includes - associative array with type/relative path pairs.
    
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
    
      $file - filepath or filename relative to root
      
    Returns:
    
      array
  */
  
  private function getArrayFromJSON($file) {
    if (file_exists($file)) $path = $file;
    elseif (file_exists($this->root . $file)) $path = $this->root . $file;
    return json_decode(@file_get_contents($path), true);
  }
  
  /*
    Method: getOutput
    
    Generates form mark-up. Recursive.
    
    Arguments:
    
      $data - json definition
    
    Returns:
    
      string
  */
  
  private function getOutput($data) {
    $output = '';
    foreach($data as $args) {
      $fn = ($this->template[$args['id']]) ? $this->template[$args['id']] : $this->template[$args['type']];
      $input = $this->getValue($args['name']);
      $vals = $args + array(
        'error' => $this->getError($args),
        'input' => $input,
        'selected' => (is_array($input)) ? in_array($args['value'], $input) : $args['value'] == $input
      );
      if (isset($args['elements'])) {
        $vals = $vals + array('content' => $this->getOutput($args['elements']));
      }
      ob_start();
      if (function_exists($fn)) $fn($vals);
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
    $output = '<input type="hidden" name="formid" value="'.$this->uid.'" />';
    
    $output .= $this->getOutput($this->definition['elements']);
    
    echo $this->template['form']($this->definition['attributes'] + array(
      'content' => $output,
      'message' => $this->getMessage()
    ));
  }
  
  /*
    Method: getMessageType
    
    Returns the status of the message. Used only when javascript isn't enabled.
    
    Returns:
    
      string 'error' or 'success'
    
  */
  
  public function getMessageType() {
    return $_SESSION['validator']['message_type'];
  }
  
  /*
    Method: printMessageType
    
    Prints status of the message.
    
    See Also:
    
      <getMessageType>
  */
  
  public function printMessageType() {
    echo $this->getMessageType();
  }
  
  /*
    Method: getMessage
    
    Gets the message created after the form has been submitted and validated.
    
    Returns:
    
      Form validation message.
  */
  
  public function getMessage() {
    $message = $_SESSION['validator']['message'];
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
    
      <getMessage>
  */
  
  public function printMessage() {
    echo $this->getMessage();
  }
  
  /*
    Method: getValue
    
    Gets the value of a submitted field.
    
    Arguments:
    
      $name - name of field
    
    Returns:
    
      Value of a submitted field.
  */
  
  public function getValue($name) {
    if (substr($name, -2) == '[]') $post_key = substr($name, 0, -2);
    return $_SESSION['validator'][$name]; 
  }
  
  /*
    Method: printValue
    
    Arguments:
    
      $name - name of field
    
    See Also:
    
      <getValue>
  */
  
  public function printValue($name) {
    echo $this->getValue($name);
  }
  
  /*
    Method: getError
    
    Get the validation error of a field after the form has been submitted.
    
    Arguments:
    
      $args - array, field arguments
    
    Returns:
    
      Field validation error or null.
  */
  
  public function getError($args) {
    if (isset($_SESSION['validator'][$args['name'] . '_error'])) {
      $error = $_SESSION['validator'][$args['name'] . '_error'];
      if (function_exists($this->template['field_error'])) {
        ob_start();
        $this->template['field_error']($args + array('message' => $error));
        $error = ob_get_contents();
        ob_end_clean();
      }
      return $error;
    } else return null;
  }
  
  /*
    Method: printError
    
    Arguments:
    
      $args - array, field arguments
    
    See Also:
    
      <getError>
  */
  
  public function printError($args) {
    $this->getError($args);
  }
  
  /*
    Method: printAssets
    
    Prints necessary css/javascript files.
    
    Arguments:
    
      $global - bool, print all required files or only those for the plugin
  */
  
  public function printAssets($global = true) {
    echo "\n\n<!-- Begin JibberBook {$this->plugin} Assets -->\n";
    if ($global) {
      echo "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></script>";
      echo "<script type=\"text/javascript\" src=\"{$this->uri}valerieclient.js\"></script>\n";
    }
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->uri}plugins/{$this->plugin}/style.css\" />\n";
    echo "<script type=\"text/javascript\" src=\"{$this->uri}plugins/{$this->plugin}/script.js\"></script>\n";
    
    foreach ($this->includes as $type => $path) {
      if (is_array($path)) {
        $conditional = $path[0];
        $path = $path[1];
        echo "<!--[if $conditional]>\n";
      }
      $path = strip_tags($path);
      switch ($type) {
        case 'css':
          echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->uri}plugins/{$this->plugin}/$path\" />\n";
          break;
        case 'js':
          echo "<script type=\"text/javascript\" src=\"{$this->uri}plugins/{$this->plugin}/$path\"></script>\n";
          break;
      }
      if (isset($conditional)) {
        echo "<![endif]-->\n";
      }
    }
    
    echo "<script type=\"text/javascript\">jQuery(function($){ $(\"#{$this->definition['attributes']['id']}\").valerie(); })</script>\n";
    echo "<!-- End JibberBook {$this->plugin} Assets -->\n\n";
  }
}
?>
