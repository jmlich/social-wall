<?php

echo "disabled"; exit();

require('init.php');


$query = <<<EOF
CREATE TABLE IF NOT EXISTS `feed` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `avatar` varchar(2048) NOT NULL,
  `author` varchar(2048) NOT NULL,
  `text` text NOT NULL,
  `image` varchar(2048) NOT NULL,
  `link` varchar(2048) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
EOF;

if (!mysqli_query($db, $query)) {
  die(mysqli_error());
}

$query = <<<EOF
CREATE TABLE IF NOT EXISTS `feed_info` (
  `key` varchar(128) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF;


if (!mysqli_query($db, $query)) {
  die(mysqli_error());
}

$query = <<<EOF
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`), ADD KEY `time` (`time`);
EOF;


if (!mysqli_query($db, $query)) {
  die(mysqli_error());
}

$query = <<<EOF
ALTER TABLE `feed_info`
  ADD PRIMARY KEY (`key`);
EOF;


if (!mysqli_query($db, $query)) {
  die(mysqli_error());
}

$query = <<<EOF
ALTER TABLE `feed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
EOF;


if (!mysqli_query($db, $query)) {
  die(mysqli_error());
}
?>