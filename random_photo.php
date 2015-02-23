<?php

require('init.php');

require('./news_api.php');

$feed = get_news(  $config['news'], array('lang' => 'cs'), $config['news_info'] );

$images = array();

foreach ($feed as $item) {
  if (isset($item['image']) && $item['image'] != "") {
    array_push($images, $item['image']);
  }
}

$images= array_unique($images);
shuffle($images);

//  <div class="js-masonry">
$content .= <<<EOF
  <div class="js-masonry" data-masonry-options='{ "itemSelector": ".item", "columnWidth": 50 }'>

EOF;

foreach ($images as $item) {
  $content .= " <img class=\"item\" src=\"$item\"/>\n";
}

$content .= <<<EOF
</div>
<script src="./js/masonry.pkgd.min.js"></script>

EOF;
/*
<script>
var container = document.querySelector('#container');
var msnry = new Masonry( container, {
  itemSelector: '.item'
  columnWidth: 200,

});
</script>
*/
require('template.php');

?>