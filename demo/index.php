<?php
// first include functions.php which is a bootstrap for everything else
include('../source/bootstrap.php');

// create a new ValerieForm instance
$form = newValerieForm();

// set form definition
$form->setDefinition('form_def.php');

// create a second form
$form2 = newValerieForm();

// set definition from array
$form2->setDefinition(array(
  'attributes' => array(
    'id' => 'frm2',
    'method' => 'post',
    'action' => 'process_form.php'
  ),
  'elements' => array(
    array(
      'type' => 'fieldset',
      'legend' => 'This is the second form.',
      'elements' => array(
        array(
          'type' => 'text',
          'label' => 'Search or something.',
          'name' => 'search',
          'id' => 'fld_search',
          'validation' => 'required',
          'filter' => 'striptags'
        ),
        array(
          'type' => 'submit',
          'value' => 'go!'
        )
      )
    )
  )
));


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Valerie Demo</title>
    
    <?php
      // print assets required for Valerie
      $form->printAssets();
    ?>
    
    <link rel="stylesheet" type="text/css" href="files/style.css" />
    <script type="text/javascript">
      // Here is a custom event to print form data on validation (for ajax).
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
    <h3>Here is one form:</h3>
    <?php
    // Here we are getting data set by my_form.php on a successful submission.
    // This does the same thing as the javascript function but for non-ajax
    // submissions.
    if ($form->getResponse('data')) : ?>
      <ul>
      <?php foreach ($form->getResponse('data') as $index => $value) : ?>
        <li><?php echo $index; ?>: <?php var_export($value); ?></li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    
    <?php
    // Render the first form
    $form->render();
    ?>
    
    <h3>Here is another form:</h3>
    <?php
    // Render the second form
    $form2->render();
    ?>     
  </div>
  </body>
</html>
