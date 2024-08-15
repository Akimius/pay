<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use App\app\Application;

$configs = require (ROOT_PATH . '/configs/config.php');
$result = (new Application($configs))->run();

echo implode(PHP_EOL, $result) . PHP_EOL;