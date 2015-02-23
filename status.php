<?php
header('content-type: application/json');
$mtime = 0;
$files = array(
  'v1.php',
  'v2.php',
  'v3.php',
  'v4.php',
);
foreach ($files as $file) {
  $mtime = max($mtime, filemtime($file));
}

echo json_encode(array(
  'last_update' => $mtime
));
?>