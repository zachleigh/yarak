# Yarak   
[![Latest Stable Version](https://img.shields.io/packagist/v/zachleigh/yarak.svg)](//packagist.org/packages/zachleigh/yarak)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](//packagist.org/packages/zachleigh/yarak)
[![Build Status](https://img.shields.io/travis/zachleigh/yarak/master.svg)](https://travis-ci.org/zachleigh/yarak)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/408a37c2-96fb-4622-95d1-9c53a0211269.svg)](https://insight.sensiolabs.com/projects/408a37c2-96fb-4622-95d1-9c53a0211269)
[![Quality Score](https://img.shields.io/scrutinizer/g/zachleigh/yarak.svg)](https://scrutinizer-ci.com/g/zachleigh/yarak/)
[![StyleCI](https://styleci.io/repos/83725289/shield?style=flat)](https://styleci.io/repos/83725289)     

*yarak - (Falconry) a state of prime fitness in a falcon*    
  
##### Laravel inspired Phalcon devtools 
  - Database migrations that rollback step-by-step, reset the database, and refresh the database.

### Contents
  - [Install](#install)
  - [Migrations](#migrations)
    - [Generating Migrations](#generating-migrations)
    - [Writing Migrations](#writing-migrations)
      - [Creating Tables](#creating-tables)
      - [Updating Tables](#updating-tables)
      - [The Down Method](#the-down-method)
    - [Running Migrations](#running-migrations)
    - [Rolling Back Migrations](#rolling-back-migrations)
    - [Resetting The Database](#resetting-the-database)
    - [Refreshing The Database](#refreshing-the-database)
  - [Contributing](#contributing)

### Install
##### Install via composer
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
                'databaseDir' => 'path/to/database/directory/',
            ],

            'database' => [
                'adapter'  => $config->database->adapter,
                'host'     => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname'   => $config->database->dbname,
                'charset'  => $config->database->charset,
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
  - Autoload all project files and vendor directory files
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
The above example is included in the project at yarak/src/yarak_example. Copy it into your project with the following command, done from the project root:
```
cp vendor/zachleigh/yarak/src/yarak_example yarak
```

Once the yarak file exists, make it executable:
```
chomd +x yarak
```
##### Add the database directory to the composer autoloader
Because migrations do not follow psr-4 naming conventions, load them with a classmap.
```
"autoload": {
    "classmap": [
        "relative/path/to/database/directory"
    ]
}
```
Test to make sure that it is working in the console:
```
php yarak
```

### Migrations
Yarak migrations provide a simple, clean way to manage your database.
  - [Generating Migrations](#generating-migrations)
  - [Writing Migrations](#writing-migrations)
    - [Creating Tables](#creating-tables)
    - [Updating Tables](#updating-tables)
    - [The Down Method](#the-down-method)
  - [Running Migrations](#running-migrations)
  - [Rolling Back Migrations](#rolling-back-migrations)
  - [Resetting The Database](#resetting-the-database)
  - [Refreshing The Database](#refreshing-the-database)

#### Generating Migrations
All migrations are stored in databaseDir/migrations. The databaseDir path may be set when [registering the Yarak service](#register-the-service).     

To generate migrations, use the `make:migration` command:
```
php yarak make:migration migration_name --create=table_name
```
The migration name must be snake_case and will be used to create the migration file name and class name. For example:
```
php yarak make:migration create_users_table
```
Using the name `create_users_table` will generate a migration class called `CreateUsersTable`. Migration file names are generated using a timestamp and the given name. In this example, the generated file name might look something like this: 2017_03_04_055719_create_users_table.php.   

If you are creating a new table, using the `--create` flag plus the name of the database table will create a migration file with some additional boiler plate to save a little time.
```
php yarak make:migration create_users_table --create=users
```

#### Writing Migrations
Yarak uses Phalcon's [Database Abstraction Layer](https://docs.phalconphp.com/en/3.0.0/reference/db.html) to interact with the database. This guide will only cover the most common operations. For more detailed information about what is possible, please see the [API Documentation](https://docs.phalconphp.com/en/3.0.1/api/Phalcon_Db_Adapter.html).   

##### Creating Tables
To create a table, use the `$connection` variable's `createTable` method.
```php
public createTable (mixed $tableName, mixed $schemaName, array $definition)
```

To create a simple users table, your `up` method might look something like this:
```php
use Phalcon\Db\Index;
use Phalcon\Db\Column;

//

public function up(Pdo $connection)
{
    $connection->createTable(
        'users',
        null,
        [
            'columns' => [
                new Column('id', [
                    'type'          => Column::TYPE_INTEGER,
                    'size'          => 10,
                    'unsigned'      => true,
                    'notNull'       => true,
                    'autoIncrement' => true
                ]),
                new Column('username', [
                    'type'    => Column::TYPE_VARCHAR,
                    'size'    => 32,
                    'notNull' => true
                ]),
                new Column('password', [
                    'type'    => Column::TYPE_CHAR,
                    'size'    => 40,
                    'notNull' => true
                ]),
                new Column('email', [
                    'type'    => Column::TYPE_VARCHAR,
                    'size'    => 20,
                    'notNull' => true
                ]),
                new Column('created_at', [
                    'type'    => Column::TYPE_TIMESTAMP,
                    'notNull' => true,
                    'default' => 'CURRENT_TIMESTAMP'
                ])
            ],
            'indexes' => [
                new Index('PRIMARY', ['id'], 'PRIMARY')
            ]
        ]
    );
}
```

The definition array must contain a `columns` array, and can also include `indexes`, `references`, and `options` arrays. To define columns use Phalcon's [DB Column class](https://docs.phalconphp.com/en/3.0.1/api/Phalcon_Db_Column.html) class, for indexes use the [DB Index class](https://docs.phalconphp.com/en/3.0.1/api/Phalcon_Db_Index.html), and for foreign keys use the [DB Reference class](https://docs.phalconphp.com/en/3.0.1/api/Phalcon_Db_Reference.html).  

For more information, see the [official documentation](https://docs.phalconphp.com/en/3.0.0/reference/db.html#creating-tables).

##### Updating Tables
To modify a column, use the `$connection` variable's `modifyColumn` method:
```php
public modifyColumn (mixed $tableName, mixed $schemaName, Phalcon\Db\ColumnInterface $column, [Phalcon\Db\ColumnInterface $currentColumn])
```
Continuing the example above, our email column size is currently set to 20 which is clearly not big enough. To modify this, we can create a new migration:
```
php yarak make:migration increase_user_email_column_size
```
In the created migration's up method, we can write the following:
```php
public function up(Pdo $connection)
{
    $connection->modifyColumn(
        'users',
        null,
        new Column(
            'email',
            [
                'type' => Column::TYPE_VARCHAR,
                'size' => 70,
            ]
        )
    );
}
```
Keep in mind that when using the Column class, `type` is required.    

To add additional columns to a table, use the `addColumn` method:
```php
public addColumn (mixed $tableName, mixed $schemaName, Phalcon\Db\ColumnInterface $column)
```
So if we want to add an `active` column to our users table, we create a new migration:
```
php yarak make:migration add_active_column_to_users_table
```
And our migration up method could look like this:
```php
public function up(Pdo $connection)
{
    $connection->addColumn(
        'users',
        null,
        new Column(
            'active',
            [
                'type'    => Column::TYPE_CHAR,
                'size'    => 1,
                'notNull' => true,
            ]
        )
    );
}
```
The [official documentation](https://docs.phalconphp.com/en/3.0.0/reference/db.html#altering-tables) contains some additional examples and information which may be helpful.

##### The Down Method
In order for migraion rollbacks to work, migrations must contain a `down` method where the process described in the `up` method is reversed. To continue our above example, when creating the users table, our down method would use the `dropTable` method:
```php
public function down(Pdo $connection)
{
    $connection->dropTable('users');
}
```

When modifying the email column, we could simply modify the column so that it returns to it's previous state:
```php
public function down(Pdo $connection)
{
    $connection->modifyColumn(
        'users',
        null,
        new Column(
            'email',
            [
                'type' => Column::TYPE_VARCHAR,
                'size' => 20,
            ]
        )
    );
}
```

When adding the `active` column, use the `dropColumn` method:
```php
public function down(Pdo $connection)
{
    $connection->dropColumn('users', null, 'active');
}
```

#### Running Migrations
To run all pending migrations, simply use the Yarak `migrate` command:
```
php yarak migrate
```

This will run all migrations that have not yet been run. Migrations that are run at the same time will be in the same 'batch' and will be rolled back together.

#### Rolling Back Migrations
:exclamation:**Before rolling back, be aware that all data in the tables you rollback will be lost.**   

To rollback the last batch of migrations, call `migrate` with the `--rollback` flag:
```
php yarak migrate --rollback
```

Use `--rollback` with the optional `--steps` flag to rollback more than one batch.
```
php yarak migrate --rollback --steps=2
```
This will rollback the last two batches of migrations.

#### Resetting The Database
Using the `--reset` flag will rollback all migrations.   

:exclamation:**Resetting the database will remove all data from your database.** Be sure any data you wish to keep is backed up before proceeding.
```
php yarak migrate --reset
```

#### Refreshing The Database
Refreshing the database will rollback all migrations and then re-run them all in a single batch.   

:exclamation:**Refreshing the database will remove all data from your database.** Be sure any data you wish to keep is backed up before proceeding.
```
php yarak migrate --refresh
```

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/zachleigh/yarak/issues).
