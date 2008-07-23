<?php
  require_once('../source/valerieserver.php');
  $myValidator = new ValerieServer($_POST);
  $ajax = $myValidator->is_ajax();
  $myValidator->register(array(
    'digit' => array('/^\d$/', 'This field must contain one numerical character.'),
    'less_than_150' => array('is_less_than_150', 'This field must contain a value less than 150.'),
    'divisible' => array('is_divisible_by', '{2} is not divisible by {1}.'),
    'sum_equals' => array('sum_equals', '{1} plus {2} equals {3}, not {4}.')
  ));
  $data = $myValidator->validate();

  function is_less_than_150($val) {
    return (int) $val < 150;
  }

  function is_divisible_by($val, $by, $err) {
    $err = ValerieServer::format($err, array($by, $val));
    return array((int) $val % (int) $by == 0, $err);    
  }
  
  function sum_equals($val, $arg, $err) {
    $result = (int) $val + (int) $arg[0];
    if($result != (int) $arg[1]) {
      $err = ValerieServer::format($err, array($arg[0], $val, $result, $arg[1]));
      return array(false, $err);
    } else return true;
  }
  
  // do stuff with the data
  
  // if it's not an ajax call, go back to the form
  $myValidator->back();
  // OR Valerie::back($ajax);
?>
