<?php

use StreetCsv\Config;

require_once '../vendor/autoload.php';

$config = new Config();

echo 'Hello World - ',get_class($config);