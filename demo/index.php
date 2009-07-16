<?php
// first include bootstrap.php
include('../source/bootstrap.php');

// create a new ValerieForm instance
$form = new ValerieForm('form_def.php', 'extended');

// create a second form
$form2 = new ValerieForm(array(
  'attributes' => array(
    'id' => 'frm2',
    'method' => 'post',
    'action' => 'process_form.php'
  ),
  'elements' => array(
    array(
      'type' => 'fieldset',
      'label' => 'This is the second form.',
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
          'label' => 'go!'
        )
      )
    )
  )
));

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Valerie Demo</title>
    
    <?php
      // print assets required for Valerie
      Valerie::loadAssets();
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
    
    <h3>The third form</h3>
    <?php
    // Render the single file setup form at 'valerie:config:single_setup'
    Valerie::render();
    ?>
    
    <h3>The last form</h3>
    <?php
    // Render the form plugin at 'valerie:forms:contact-form'
    Valerie::render('contact-form');
    ?>
  </div>
  </body>
</html>
<?php Valerie::stopProfiler(); ?>
