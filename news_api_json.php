<?php
  require('./news_api.php');
date_default_timezone_set('Europe/Prague');

//$feed = download_news(  $config['news'], array('lang' => 'cs'));
$feed = get_news(  $config['news'], array('lang' => 'cs'), $config['news_info']);
if (isset($_REQUEST['images'])) {
  $feed = get_cache( 1 );
} else {
  $feed = get_cache( );
//  $feed = get_cache( NULL, NULL );
}

if (isset($_REQUEST['debug'])) {
  echo "<pre>";
  print_r($feed);
  echo "</pre>";
  exit();
}

header('content-type: application/json');
echo json_encode($feed);
?>