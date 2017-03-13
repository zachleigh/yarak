<?php

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;

$loader = new Loader();

$loader->registerNamespaces([
    'App\Models' => APP_PATH.'/models',
]);

$loader->register();

/**
 * Composer
 */
// require_once BASE_PATH . '/vendor/autoload.php';
