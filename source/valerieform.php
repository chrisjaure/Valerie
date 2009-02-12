<?php    
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
        $this->setDefinition('forms/default.json');
        $this->loadPlugin($plugin);
    }
    
    public function setTemplate($form) {
        if (is_array($form)) $this->template = array_merge($this->template, $form);
        else {
            $file = $this->getArrayFromJSON($form);
            if (is_array($file))
                $this->template = array_merge($this->template, $file);
        }
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
        //$this->setTemplate('plugins/'.$this->plugin.'/template.json');
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
            'error' => $this->getError($args['id']),
            'value' => $this->getValue($args['id'])
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
    
    public function getMessage($format = null) {
        if ($format) {
            return str_replace(array('{message}', '{type}'), array($_SESSION['validator']['message'], $this->getMessageType()), $format);
        } else return $_SESSION['validator']['message'];
    }
    
    public function printMessage($format = null) {
        $template = ($format) ? $format : $this->formats['form_message'];
        echo $this->getMessage($format);
    }
    
    public function getValue($id) {
        return $_SESSION['validator'][$id]; 
    }
    
    public function printValue($id) {
        echo $this->getValue($id);
    }
    
    public function getError($id, $format = null) {
        $template = ($format) ? $format : $this->formats['field_error'];
        if (isset($_SESSION['validator'][$id . '_error'])) {
            if ($template) {
                return str_replace('{message}', $_SESSION['validator'][$id . '_error'], $template);
            } else return $_SESSION['validator'][$id . '_error'];
        } else return null;
    }
    
    public function printError($id, $format = null) {
        echo $this->getError($id, $format);
    }
}
?>
