<?php
session_start();
$_SESSION['referer'] = $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <title>Valerie Demo</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
    <script type="text/javascript" src="../source/adapter/jquery-1.2.6.js"></script>
    <script type="text/javascript" src="../source/valerieclient.js"></script>
    <script type="text/javascript" src="../source/plugins/fancy/fancy_jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="files/reset.css" />
    <link rel="stylesheet" type="text/css" href="files/generic.css" />
    <link rel="stylesheet" type="text/css" href="files/style.css" />
    <link rel="stylesheet" type="text/css" href="../source/plugins/fancy/fancy.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    $(function() {
      new ValerieClient('frm', {'validateField':true}, fancy_jquery);
    });
    </script>
  </head>
  <body>
  <div class="content">
  <h1>Valerie Demo</h1>
  <h2>Using Ajax to validate form data server-side.</h2>
    <?php echo $_SESSION['validator']['message']; ?>
    <form id="frm" method="post" action="my_form.php">
      <label for="f1">Your Name:</label><input type="text" name="f1:required|alpha" id="f1" value="<?php echo $_SESSION['validator']['f1']; ?>" /><?php echo $_SESSION['validator']['f1_error']; ?>
      <label for="f2">Password:</label><input type="password" name="f2:required|alphanumeric|minlength(6)" id="f2" value="<?php echo $_SESSION['validator']['f2']; ?>" /><?php echo $_SESSION['validator']['f2_error']; ?>
      <label for="f3">Confirm Password:</label><input type="password" name="f3:required|confirm(f2,Password)" id="f3" value="<?php echo $_SESSION['validator']['f3']; ?>" /><?php echo $_SESSION['validator']['f3_error']; ?>
      <label for="f4">Birthday:</label><input type="text" name="f4:date" id="f4" value="<?php echo $_SESSION['validator']['f4']; ?>" /><?php echo $_SESSION['validator']['f4_error']; ?>
      <label for="f5">Age:</label><input type="text" name="f5:int|less_than_150" id="f5" value="<?php echo $_SESSION['validator']['f5']; ?>" /><?php echo $_SESSION['validator']['f5_error']; ?>
      <label for="f6">Time:</label><input type="text" name="f6:time" id="f6" value="<?php echo $_SESSION['validator']['f6']; ?>" /><?php echo $_SESSION['validator']['f6_error']; ?>
      <label for="f7">How much money you got?</label><input type="text" name="f7:currency" id="f7" value="<?php echo $_SESSION['validator']['f7']; ?>" /><?php echo $_SESSION['validator']['f7_error']; ?>
      <label for="f16">How much will you give me?</label><input type="text" name="f16:currency|requiredif(f7)" id="f16" value="<?php echo $_SESSION['validator']['f16']; ?>" /><?php echo $_SESSION['validator']['f16_error']; ?>
      <label for="f8">Phoney Phone Number:</label><input type="text" name="f8:phone" id="f8" value="<?php echo $_SESSION['validator']['f8']; ?>" /><?php echo $_SESSION['validator']['f8_error']; ?>
      <label for="f9">Zip Code:</label><input type="text" name="f9:zip" id="f9" value="<?php echo $_SESSION['validator']['f9']; ?>" /><?php echo $_SESSION['validator']['f9_error']; ?>
      <label for="f10">Website:</label><input type="text" name="f10:url" id="f10" value="<?php echo $_SESSION['validator']['f10']; ?>" /><?php echo $_SESSION['validator']['f10_error']; ?>
      <label for="f11">Different Website:</label><input type="text" name="f11:differ(f10,Website)|url" id="f11" value="<?php echo $_SESSION['validator']['f11']; ?>" /><?php echo $_SESSION['validator']['f11_error']; ?>
      <label for="f12">Something between 2 and 10 characters:</label><input type="text" name="f12:minlength(2)|maxlength(10)" id="f12" value="<?php echo $_SESSION['validator']['f12']; ?>" /><?php echo $_SESSION['validator']['f12_error']; ?>
      <label for="f13">A number between 0 and 9:</label><input type="text" name="f13:digit" id="f13" value="<?php echo $_SESSION['validator']['f13']; ?>" /><?php echo $_SESSION['validator']['f13_error']; ?>
      <label for="f14">A number divisible by 4:</label><input type="text" name="f14:int|divisible(4)" id="f14" value="<?php echo $_SESSION['validator']['f14']; ?>" /><?php echo $_SESSION['validator']['f14_error']; ?>
      <label for="f15">Fill in the blank: 2 + _ = 10</label><input type="text" name="f15:int|sum_equals(2,10)" id="f15" value="<?php echo $_SESSION['validator']['f15']; ?>" /><?php echo $_SESSION['validator']['f15_error']; ?>
      <input type="submit" value="Submit!" />
    </form>
  </div>
  </body>
</html>
