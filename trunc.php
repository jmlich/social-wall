<?php

echo "disabled"; exit();

require_once realpath(dirname(__FILE__) . '/init.php');
require_once realpath(dirname(__FILE__) . '/xml.php');

    $query = "TRUNCATE `feed`";
    if (!($result = mysqli_query($db, $query))) {
      throw new Exception(mysqli_error($db));
    }

    $query = "TRUNCATE `feed_info`";
    if (!($result = mysqli_query($db, $query))) {
      throw new Exception(mysqli_error($db));
    }


?>