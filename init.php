<?php

error_reporting(E_ALL);
setlocale(LC_TIME, "cs_CZ.UTF-8");
date_default_timezone_set('Europe/Prague');

require_once(realpath(dirname(__FILE__) . '/config.php'));
//require_once('config.php');

function mysqli_escape_my($str) {
  global $db;
  if (get_magic_quotes_gpc()) {
    return mysqli_real_escape_string($db, stripslashes($str));
  } else {
    return mysqli_real_escape_string($db, $str);
  }
}

if (!isset($db)) {
  $db = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'] );
  if (!$db) { 
    throw new Exception('Cannot connect to database'); 
  }


  if (!(mysqli_query($db, "SET CHARACTER SET utf8"))) { 
    throw new Exception (mysqli_error($db)); 
  }

}

$content ='';


?>