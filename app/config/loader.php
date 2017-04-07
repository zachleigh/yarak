<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces([
    'MyApp\Models'           => APP_PATH.'/models',
    'MyApp\Console'          => APP_PATH.'/console',
    'MyApp\Console\Commands' => APP_PATH.'/console/commands',
]);

$loader->register();

require_once BASE_PATH.'/vendor/autoload.php';
