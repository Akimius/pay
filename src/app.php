<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

//use App\app\Application;
//
//$configs = require (ROOT_PATH . '/configs/config.php');
//$result = (new Application($configs))->run();
//
//echo implode(PHP_EOL, $result) . PHP_EOL;

echo 'hello world' . PHP_EOL;

$output = shell_exec("python3 script.py 2 2");

echo $output;
