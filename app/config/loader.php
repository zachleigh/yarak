<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces([
    'App\Models' => APP_PATH.'/models',
]);

$loader->register();
