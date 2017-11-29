<?php

use Phalcon\Config;

defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__).'/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH.'/app');

return new Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'root',
        'password'    => 'Eh2prsEh',
        'dbname'      => 'yarak',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH.'/',
        'commandsDir'    => APP_PATH.'/console/commands',
        'consoleDir'     => APP_PATH.'/console/',
        'databaseDir'    => APP_PATH.'/database/',
        'migrationsDir'  => APP_PATH.'/database/migrations/',
        'modelsDir'      => APP_PATH.'/models/',
    ],
    'namespaces' => [
        'root' => 'MyApp',
    ],
]);
