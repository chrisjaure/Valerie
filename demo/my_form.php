<?php
  require_once('../source/functions.php');

  // extra validation rules
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

  // Create a new instance of ValerieServer
  $myValidator = newValerieServer($_POST);
  
  // Register any additional rules
  $myValidator->registerRules(array(
    'digit' => array('/^\d$/', 'This field must contain one numerical character.'),
    'less_than_150' => array('is_less_than_150', 'This field must contain a value less than 150.'),
    'divisible' => array('is_divisible_by', '{2} is not divisible by {1}.'),
    'sum_equals' => array('sum_equals', '{1} plus {2} equals {3}, not {4}.')
  ));
  
  // Validate data, returns data on success or false in failure
  $data = $myValidator->validate();
  
  if ($data) {
    // Do stuff with the data, here we are setting additional data to use on
    // the form page. In this case, we're just returning the filtered form data.
    $myValidator->setResponse('data', $data);
    
    // Optionally, you can redirect to a different page with goto
    // $myValidator->goto('http://google.com/');
  }
  
  // This function outputs any json if ajax is used, or redirects back to form
  // page if it's a regular form submission
  $myValidator->back();
  //var_dump($_SESSION[ValerieConfig::SESSION_NS]);
?>
