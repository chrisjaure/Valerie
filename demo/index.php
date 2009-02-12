<?php
include('../source/valerieform.php');
$form = new ValerieForm('default');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <title>Valerie Demo</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
    
    <script type="text/javascript" src="../source/valerieclient.js"></script>
    <link href="files/tripoli.simple.css" type="text/css" rel="stylesheet"> 
    <!--[if IE]><link rel="stylesheet" type="text/css" href="files/tripoli.simple.ie.css"><![endif]-->
    <link rel="stylesheet" type="text/css" href="files/style.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>
  <div class="content">
  <h1>Valerie Demo</h1>
  <h2>Using Ajax to validate form data server-side.</h2>
    <?php $form->printMessage(); ?>
    <?php $form->render(); ?>     
  </div>
  </body>
</html>
