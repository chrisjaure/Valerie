<?php
  require_once('../source/bootstrap.php');

  // extra validation rules
  function is_less_than_150($args) {
    extract($args);
    return (int) $value < 150;
  }

  function is_divisible_by($args, $Valerie) {
    extract($args);
    // $arguments = number to divide by
    $err = $Valerie->format($error, array($arguments, $value));
    return array((int) $value % (int) $arguments === 0, $err);
  }

  // Create a new instance of ValerieServer
  $myValidator = newValerieServer($_POST);
  
  // Register any additional rules
  $myValidator->registerRules(array(
    'digit' => array('/^\d$/', 'This field must contain one numerical character.'),
    'less_than_150' => array('is_less_than_150', 'This field must contain a value less than 150.'),
    'divisibleby' => array('is_divisible_by', '{2} is not divisible by {1}.')
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
