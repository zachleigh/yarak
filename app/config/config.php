<?php

use Phalcon\Config;

defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__).'/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH.'/app');

return new Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'root',
        'password'    => 'password',
        'dbname'      => 'yarak',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH.'/',
        'modelsDir'      => APP_PATH.'/models/',
        'migrationsDir'  => APP_PATH.'/database/migrations/',
        'baseUri'        => '/',
    ],
]);
