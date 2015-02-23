<?php

require('config.php');

$content .= <<<EOF

<section class="section-normal section-link" >

<div class="col-sm-3">

  <div class="feed_item">
    <a href="v1.php"> with ads and css changes </a>
  </div>

  <div class="feed_item">
    <a href="v2.php"> basic dark </a>
  </div>

  <div class="feed_item">
    <a href="v3.php"> basic light </a>
  </div>

  <div class="feed_item">
    <a href="v4.php"> animated social wall</a>
  </div>

  <div class="feed_item">
    <a href="v5.php"> animated images </a>
  </div>

  <div class="feed_item">
    <a href="debug_feed.php"> debug feed </a>
  </div>

</div>

</section>
<div class="col-sm-3">

EOF;


foreach ($config['pages'] as $page) {
  $url = $page['url'];
  $time = $page['time'];
  $content .= <<<EOF
  <div class="feed_item">
    <a href="$url">$url</a>
    ${time} s
  </div>
EOF;
}
  $content .= <<<EOF
  </div>
  <script>
  function v1() {
    window.location.href = "./v1.php";
  }
  setInterval(v1, 60000);
  </script>
EOF;

require('template.php');

?>