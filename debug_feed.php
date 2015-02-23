<?php
  require('./news_api.php');
 date_default_timezone_set('Europe/Prague');

$now = date('H:i:s');

//$feed = get_news(  $config['news'], array('lang' => 'cs'), $config['news_info'] );
$feed = download_news(  $config['news'], array('lang' => 'cs'), $config['news_info'] );
$feed = array_reverse($feed);

$content = <<<EOF

<style>
  body {overflow: auto;}
</style>

<div class="col-sm-8" role="main">
<section class="section-normal section-link" >
<div id="feed">
  <div id="feed_content">
EOF;

foreach ($feed as $item) {
//  $content .= "<pre>".print_r($item, true)."</pre>";

  $content .= "<div class=\"feed_item\">";
//  $content .= "<pre>".print_r($item, true)."</pre>";
  $content .= "<img src=\"img/".$item['type'].".png\" class=\"feed_type\"/>\n";
  $content .= "<img src=\"".$item['avatar']."\" class=\"feed_avatar img-rounded\"/>\n";
  $content .= "<div class=\"feed_title\">";
  $content .= "  <div class=\"feed_author\">".$item['author']."</div> ";
  $content .= "  <div class=\"feed_time\">".time2str($item['time'])."</div>";
//  $content .= "  <div class=\"feed_time\">".date('H:i:s',$item['time'])."</div>";
  $content .= "</div>\n";
  $content .= "<div class=\"feed_text\">".$item['text']."</div>";
  $content .= (isset($item['image']) && ($item['image'] != '')) ? "<img src=\"".$item['image']."\"/ class=\"feed_image\">\n" : '';
  $content .= "<a href=\"".$item['link']."\">link</a>";
  $content .= "</div>\n\n";

}

$content .= <<<EOF

</div>
<div style="clear:both;">&nbsp;</div>
</div>
</section>

</div>
EOF;

require('template.php');
?>