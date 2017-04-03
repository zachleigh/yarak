<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces([
    'App\Models' => APP_PATH.'/models',
    'App\Console' => APP_PATH.'/console',
    'App\Console\Commands' => APP_PATH.'/console/commands',
]);

$loader->register();

require_once BASE_PATH.'/vendor/autoload.php';
