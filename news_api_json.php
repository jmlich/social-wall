<?php
  require('./news_api.php');
date_default_timezone_set('Europe/Prague');

//$feed = download_news(  $config['news'], array('lang' => 'cs'));
$feed = get_news(  $config['news'], array('lang' => 'cs'), $config['news_info']);

header('content-type: application/json');
echo json_encode($feed);
?>