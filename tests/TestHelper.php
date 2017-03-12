<?php

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql;

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__.'/app/');

set_include_path(
    ROOT_PATH.PATH_SEPARATOR.get_include_path()
);

$loader = new Loader();

$loader->registerNamespaces([
    'App\Models' => ROOT_PATH.'models',
]);

$loader->register();

$di = new FactoryDefault();

Di::reset();

$di->setShared('db', function () {
    $params = [
        'adapter'  => 'Mysql',
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => 'password',
        'dbname'   => 'yarak',
        'charset'  => 'utf8',
    ];

    $connection = new MySql($params);

    return $connection;
});

Di::setDefault($di);
