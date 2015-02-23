<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="openalt">
    <link rel="icon" href="./img/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="./img/favicon.png" type="image/png" />

    <title>Developer Conference <?php echo isset($title) ? "- " . $title : ""; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style1.css" rel="stylesheet">
    <link href="css/style_all.css" rel="stylesheet">

  </head>
    <script src="./js/jquery.min.js"></script>

  <body>


<?php

echo (isset($content)) ? $content : '';

?>


    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

<div style="clear: both; height: 40px;">&nbsp;</div>

  </body>
</html>
