<?php
include('../source/functions.php');
$form = newValerieForm('default');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Valerie Demo</title>
    <?php $form->printAssets(); ?>
    <link rel="stylesheet" type="text/css" href="files/style.css" />
    <script type="text/javascript">
      $(function(){
        var output = $('<ul></ul>').insertBefore('#frm');
        $('#frm').bind('valerie.formValidated', function(e,a,b,response) {
          var html = '';
          $.each(response.data, function(index, value){
            html += '<li>' + index + ': ' + value.toString();
          });
          output.html(html);
        });
      });
    </script>
  </head>
  <body>
  <div class="content">
  <h1>Valerie Demo</h1>
  <h2>Using Ajax to validate form data server-side.</h2>
    <?php if ($form->getValue('data')) : ?>
      <ul>
      <?php foreach ($form->getValue('data') as $index => $value) : ?>
        <li><?php echo $index; ?>: <?php var_dump($value); ?></li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php $form->render(); ?>     
  </div>
  </body>
</html>
