<?php
include('../source/valeriehelper.php');
$helper = new ValerieHelper('<span class="validator_error_wrapper"><span class="validator_error"><p class="content">{message}</p><div class="bottom"></div></span></span>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <title>Valerie Demo</title>
    <!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
    <script type="text/javascript" src="../source/adapter/jquery-1.2.6-adapter.js"></script>
    <script type="text/javascript" src="../source/plugins/fancy/fancy_jquery.js"></script>-->
    
    <script type="text/javascript" src="files/mootools-1.2-core-yc.js"></script>
    <script type="text/javascript" src="../source/adapter/mootools-1.2-adapter.js"></script>
    <script type="text/javascript" src="../source/plugins/fancy/fancy_mootools.js"></script>
    
    <script type="text/javascript" src="../source/valerieclient.js"></script>
    <link href="files/tripoli.simple.css" type="text/css" rel="stylesheet"> 
    <!--[if IE]><link rel="stylesheet" type="text/css" href="files/tripoli.simple.ie.css"><![endif]-->
    <link rel="stylesheet" type="text/css" href="files/style.css" />
    <link rel="stylesheet" type="text/css" href="../source/plugins/fancy/fancy.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    window.addEvent('load', function() {
      new ValerieClient('frm', {'validateField':true}, fancy_mootools);
    });
    </script>
  </head>
  <body>
  <div class="content">
  <h1>Valerie Demo</h1>
  <h2>Using Ajax to validate form data server-side.</h2>
    <?php $helper->printMessage('<p class="{type}">{message}</p>'); ?>
    <form id="frm" method="post" action="my_form.php">
      <label for="f1">Your Name:</label><input type="text" name="f1:required|alpha" id="f1" value="<?php $helper->printValue('f1'); ?>" /><?php $helper->printError('f1'); ?>
      <label for="f2">Password:</label><input type="password" name="f2:required|alphanumeric|minlength(6)" id="f2" value="<?php $helper->printValue('f2'); ?>" /><?php $helper->printError('f2'); ?>
      <label for="f3">Confirm Password:</label><input type="password" name="f3:required|confirm(f2,Password)" id="f3" value="<?php $helper->printValue('f3'); ?>" /><?php $helper->printError('f3'); ?>
      <label for="f4">Birthday:</label><input type="text" name="f4:date" id="f4" value="<?php $helper->printValue('f4'); ?>" /><?php $helper->printError('f4'); ?>
      <label for="f5">Age:</label><input type="text" name="f5:int|less_than_150" id="f5" value="<?php $helper->printValue('f5'); ?>" /><?php $helper->printError('f5'); ?>
      <label for="f6">Time:</label><input type="text" name="f6:time" id="f6" value="<?php $helper->printValue('f6'); ?>" /><?php $helper->printError('f6'); ?>
      <label for="f7">How much money you got?</label><input type="text" name="f7:currency" id="f7" value="<?php $helper->printValue('f7'); ?>" /><?php $helper->printError('f7'); ?>
      <label for="f16">How much will you give me?</label><input type="text" name="f16:currency|requiredif(f7)" id="f16" value="<?php $helper->printValue('f16'); ?>" /><?php $helper->printError('f16'); ?>
      <label for="f8">Phoney Phone Number:</label><input type="text" name="f8:phone" id="f8" value="<?php $helper->printValue('f8'); ?>" /><?php $helper->printError('f8'); ?>
      <label for="f9">Zip Code:</label><input type="text" name="f9:zip" id="f9" value="<?php $helper->printValue('f9'); ?>" /><?php $helper->printError('f9'); ?>
      <label for="f10">Website:</label><input type="text" name="f10:url" id="f10" value="<?php $helper->printValue('f10'); ?>" /><?php $helper->printError('f10'); ?>
      <label for="f11">Different Website:</label><input type="text" name="f11:differ(f10,Website)|url" id="f11" value="<?php $helper->printValue('f11'); ?>" /><?php $helper->printError('f11'); ?>
      <label for="f12">Something between 2 and 10 characters:</label><input type="text" name="f12:minlength(2)|maxlength(10)" id="f12" value="<?php $helper->printValue('f12'); ?>" /><?php $helper->printError('f12'); ?>
      <label for="f13">A number between 0 and 9:</label><input type="text" name="f13:digit" id="f13" value="<?php $helper->printValue('f13'); ?>" /><?php $helper->printError('f13'); ?>
      <label for="f14">A number divisible by 4:</label><input type="text" name="f14:int|divisible(4)" id="f14" value="<?php $helper->printValue('f14'); ?>" /><?php $helper->printError('f14'); ?>
      <label for="f15">Fill in the blank: 2 + _ = 10</label><input type="text" name="f15:int|sum_equals(2,10)" id="f15" value="<?php $helper->printValue('f15'); ?>" /><?php $helper->printError('f15'); ?>
      <input type="submit" value="Submit!" />
    </form>
  </div>
  </body>
</html>