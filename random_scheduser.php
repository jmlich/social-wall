<?php

$data = json_decode(file_get_contents('./sched.org/_users.json'), true);

$images = array();
foreach ($data as $index => $item) {
  if (($item['avatar'] == '') || (trim($item['name']) == '')) {
    unset($data[$index]);
  }
}
shuffle($data);

$content = '';
$content .= <<<EOF
    <link href="./pages/css/speakers.css" rel="stylesheet">

<div class="speakers">
EOF;

foreach ($data as $item) {
  $name = $item['name'];
  $avatar = $item['avatar'];
  $content .= <<<EOF
    <div>
       <img class="avatar" src="$avatar"/>
       <!-- div class="name">$name</div -->
    </div>
EOF;
}

$content .= <<<EOF
</div>
EOF;

require('template.php');


?>