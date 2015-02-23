<?php

require('config.php');

header('content-type: application/json');
echo json_encode($config['pages']);