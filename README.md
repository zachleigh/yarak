# Yarak   
*yarak - (Falconry) a state of prime fitness in a falcon*    

[![Latest Stable Version](https://img.shields.io/packagist/v/zachleigh/yarak.svg)](//packagist.org/packages/zachleigh/yarak)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](//packagist.org/packages/zachleigh/yarak)
[![Build Status](https://img.shields.io/travis/zachleigh/yarak/master.svg)](https://travis-ci.org/zachleigh/yarak)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/408a37c2-96fb-4622-95d1-9c53a0211269.svg)](https://insight.sensiolabs.com/projects/408a37c2-96fb-4622-95d1-9c53a0211269)
[![Quality Score](https://img.shields.io/scrutinizer/g/zachleigh/yarak.svg)](https://scrutinizer-ci.com/g/zachleigh/yarak/)
[![StyleCI](https://styleci.io/repos/83725289/shield?style=flat)](https://styleci.io/repos/83725289)
  
##### Laravel inspired Phalcon devtools. 
  - Database migrations

### Contents
  - [Install](#install)
  - [Migrations](#migrations)
  - [Contributing](#contributing)

### Install
###### Install via composer
```
composer require zachleigh/yarak
```
##### Register the service
```php
$di->setShared('yarak',function () {
    $config = $this->getConfig();

    return new \Yarak\Kernel(
        [
            'application' => [
                'databaseDir' => __DIR__.'/app/database/',
            ],

            'database' => [
                'adapter'  => '',
                'host'     => '',
                'username' => '',
                'password' => '',
                'dbname'   => '',
                'charset'  => '',
            ],

            'yarak' => [
                'migrationRepository' => 'database',
            ],
        ]);
    }
);
```
##### Create a yarak file
In the project root, create a file called `yarak`. This file needs to do the following:
  - Load all project files
  - Load the project services
  - Resolve the Yarak kernel from the service container and call the `handle` method on it

Example:
```php
#!/usr/bin/env php
<?php

use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

/*
|--------------------------------------------------------------------------
| Autoload The Application
|--------------------------------------------------------------------------
|
| In order to work properly, Yarak will need both your project files and the
| vendor folder to be autoloaded.
|
*/
include APP_PATH . '/config/loader.php';

/*
|--------------------------------------------------------------------------
| Register The App Services
|--------------------------------------------------------------------------
|
| We need to register the app services in order to spin up Yarak. Be sure you
| have registered Yarak in the services file.
|
*/
$di = new FactoryDefault();

include APP_PATH . '/config/services.php';

/*
|--------------------------------------------------------------------------
| Handle The Incoming Commands
|--------------------------------------------------------------------------
|
| We'll get the Yarak kernel from the dependency injector and defer to it for 
| command handling.
|
*/
$kernel = $di->getYarak();

$kernel->handle();
```
Once this file is created, make it executable:
```
chomd +x yarak
```
##### Add the database directory to the composer autoloader
Because migrations do not follow psr-4 naming conventions, load them with a classmap.
```
"autoload": {
    "classmap": [
        "app/database"
    ]
}
```
Test to make sure that it is working in the console:
```
php yarak
```

### Migrations

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/zachleigh/yarak/issues).
