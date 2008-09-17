<?php
//------------------------------------------------------------------------------
//	Valerie v0.5
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	valeriehelper.php
//------------------------------------------------------------------------------

class ValerieHelper {
    
    var $format;
    
    function ValerieHelper($format = null) {
        @session_start();
        $_SESSION['referer'] = $_SERVER['PHP_SELF'];
        $this->format = $format;
    }
    
    function getMessageType() {
        return $_SESSION['validator']['message_type'];
    }
    
    function printMessageType() {
        echo $this->getMessageType();
    }
    
    function getMessage($format = null) {
        if ($format) {
            return str_replace(array('{message}', '{type}'), array($_SESSION['validator']['message'], $this->getMessageType()), $format);
        } else return $_SESSION['validator']['message'];
    }
    
    function printMessage($format = null) {
        echo $this->getMessage($format);
    }
    
    function getValue($id) {
        return $_SESSION['validator'][$id]; 
    }
    
    function printValue($id) {
        echo $this->getValue($id);
    }
    
    function getError($id, $format = null) {
        $template = ($format) ? $format : $this->format;
        if (isset($_SESSION['validator'][$id . '_error'])) {
            if ($template) {
                return str_replace('{message}', $_SESSION['validator'][$id . '_error'], $template);
            } else return $_SESSION['validator'][$id . '_error'];
        } else return null;
    }
    
    function printError($id, $format = null) {
        echo $this->getError($id, $format);
    }
    
}

?>