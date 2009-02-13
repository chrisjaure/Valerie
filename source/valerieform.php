<?php
//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerieform.php
//------------------------------------------------------------------------------
  
class ValerieForm {
    private $template = array();
    private $definition;
    private $output;
    private $replace;
    private $formats = array();
    private $root;
    private $plugin;
    private $uid;
    
    public function __construct($plugin='default') {
        @session_start();
        $_SESSION['validator']['referer'] = $_SERVER['PHP_SELF'];
        $this->root = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . '/';
        $this->uid = md5('randomness'.time());
        $this->loadPlugin($plugin);
        $this->setDefinition('forms/default.json');
    }
    
    public function setTemplate($form) {
        if (is_array($form)) $this->template = array_merge($this->template, $form);
    }
    
    public function setDefinition($definition) {
        if (is_array($definition)) $this->definition = $definition;
        else {
            $this->definition = $this->getArrayFromJSON($definition);
        }
        $_SESSION['validator'][$this->uid] = serialize($this->definition);
    }
    
    private function loadPlugin($name) {
        $this->plugin = $name;
        include($this->root.'plugins/'.$this->plugin.'/template.php');
    }
    
    private function getArrayFromJSON($file) {
        if (file_exists($file)) $path = $file;
        elseif (file_exists($this->root . $file)) $path = $this->root . $file;
        return json_decode(@file_get_contents($path), true);
    }
    
    private function getOutput($data) {
      $output = '';
      foreach($data as $args) {
        $fn = ($this->template[$args['id']]) ? $this->template[$args['id']] : $this->template[$args['type']];
        $vals = $args + array(
            'error' => $this->getError($args['name']),
            'value' => $this->getValue($args['name'])
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
    
    public function render() {
        $output = '<input type="hidden" name="formid" value="'.$this->uid.'" />';
        
        $output .= $this->getOutput($this->definition['elements']);
        
        echo '<link rel="stylesheet" type="text/css" href="../source/plugins/', $this->plugin, '/style.css" />';
        echo $this->template['form']($this->definition['attributes'] + array('content' => $output));
        echo '<script type="text/javascript" src="../source/plugins/', $this->plugin, '/script.js"></script>';
        echo '<script type="text/javascript">jQuery("#', $this->definition['attributes']['id'], '").valerie();</script>';
    }
    
    public function getMessageType() {
        return $_SESSION['validator']['message_type'];
    }
    
    public function printMessageType() {
        echo $this->getMessageType();
    }
    
    public function getMessage() {
        $message = $_SESSION['validator']['message'];
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
    
    public function printMessage() {
        echo $this->getMessage();
    }
    
    public function getValue($name) {
        return $_SESSION['validator'][$name]; 
    }
    
    public function printValue($name) {
        echo $this->getValue($name);
    }
    
    public function getError($name) {
        if (isset($_SESSION['validator'][$name . '_error'])) {
            $error = $_SESSION['validator'][$name . '_error'];
            if (function_exists($this->template['field_error'])) {
              ob_start();
              $this->template['field_error'](array('message' => $error));
              $error = ob_get_contents();
              ob_end_clean();
            }
            return $error;
        } else return null;
    }
    
    public function printError($name) {
        $this->getError($name);
    }
}
?>
