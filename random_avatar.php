<?php

require('init.php');

require('./news_api.php');

$feed = get_news(  $config['news'], array('lang' => 'cs'), $config['news_info'] );

$images = array();

foreach ($feed as $item) {
  array_push($images, $item['avatar']);
}
$images = array_unique ($images);
shuffle($images);


//  <div class="js-masonry">
$content .= <<<EOF
<div class="js-masonry avatars-masonry">
EOF;

foreach ($images as $item) {
  $content .= " <img src=\"$item\"/>\n";
}

$content .= <<<EOF
</div>
EOF;

require('template.php');

?>