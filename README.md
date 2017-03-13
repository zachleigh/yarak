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
  - Model factories for easy test data creation.

### Contents
  - [Install](#install)
  - [Database](#database)
    - [Generating Directories And Files](#generating-directories-and-files)
    - [Model Factories](#model-factories)
      - [Defining Factories](#defining-factories)
      - [Using The Factory Helper](#using-the-factory-helper)
      - [Making Multiple Model Instances](#making-multiple-model-instances)
      - [Overriding The Default Attributes](#overriding-the-default-attributes)
      - [Model Relationships](#model-relationships)
      - [Using Named Factories](#using-named-factories)
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
  - [Calling Yarak In Code](#calling-yarak-in-code)
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

### Database
Yarak gives users several helpful database functionalities that make development easier.
  - [Generating Directories And Files](#generating-directories-and-files)
  - [Model Factories](#model-factories)
    - [Defining Factories](#defining-factories)
    - [Using The Factory Helper](#using-the-factory-helper)
    - [Making Multiple Model Instances](#making-multiple-model-instances)
    - [Overriding The Default Attributes](#overriding-the-default-attributes)
    - [Model Relationships](#model-relationships)
    - [Using Named Factories](#using-named-factories)

#### Generating Directories And Files
All database and migration functionalites require a standardized file hierarchy. To generate this hirearchy, use the `db:generate` command:
```
php yarak db:generate
```
This will create a database directory at the path set in the Yarak config. The database directory will contain migration, seeder, and factory directories and some file stubs to help you get started. 

#### Model Factories
Model factories provide a simple way to create testing data using the [Faker library](https://github.com/fzaninotto/Faker).

##### Defining Factories
Model factories are located in the `/database/factories` directory. This directory and a stub factory file can be created using the `php yarak db:generate` command.    

To define a factory, use the `define` method on a variable called `$factory`. The `define` method has the following method signature:
```php
public function define($class, callable $attributes, $name = 'default')
```
The first argument is the full name/namespace of the class. The second argument is a callback that returns an array. This array must contain the data necessary to create the model. The third optional argument is a name for the factory. Setting the name allows you to define multiple factories for a single model.

To create a simple user model factory:
```php
use App\Models\Users;

$factory->define(Users::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => 'password',
    ];
});
```

To create a named user model factory:
```php
use App\Models\Users;

$factory->define(Users::class, function (Faker\Generator $faker) {
    return [
        'username' => 'myUsername',
        'email' => 'myEmail',
        'password' => 'myPassword',
    ];
}, 'myUser');
```

The ModelFactory class responsible for creating model instances extends Phalcon\Mvc\User\Component and has access to the DI and any services registered. To access the ModelFactory class, use the `$factory` variable in the `$attributes` closure.
```php
use App\Models\Users;

$factory->define(Users::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => $factory->security->hash('password'),
    ];
});
```

##### Using The Factory Helper
Yarak comes with a global `factory` helper function to make creating model instances simple. The factory function returns an instance of ModelFactoryBuilder which can be used to either make or create models. Calling `make` on the returned class simply makes the model class, but does not persist the data in the database. Calling `create` creates the class and persists it in the database.   

Make a user model isntance, but don't persist it:
```php
use App\Models\Users;

$user = factory(Users::class)->make();
```

Create a user model and persist it:
```php
use App\Models\Users;

$user = factory(Users::class)->create();
```

##### Making Multiple Model Instances
If you require multiple instances of the model class, pass an integer as the second argument to `factory`:
```php
use App\Models\Users;

// Make three users
$users = factory(Users::class, 3)->make();

// Create three users
$users = factory(Users::class, 3)->create();
```
When more than one model is made, an array of models is returned.

##### Overriding The Default Attributes
To override the default attributes set in the factory definition, pass an array of overrides to `make` or `create`:
```php
use App\Models\Users;

// Make a user with username 'bobsmith' and email 'bobsmith@example.com'
$user = factory(Users::class)->make([
    'username' => 'bobsmith',
    'email'    => 'bobsmith@example.com'
]);

// Create a user with username 'bobsmith' and email 'bobsmith@example.com'
$user = factory(Users::class)->create([
    'username' => 'bobsmith',
    'email'    => 'bobsmith@example.com'
]);
```

##### Model Relationships
When making model instances that require model relationships to also be built, you have a few options.   

You can manually create related models. In this example, we have Posts and Users which have a one-to-many relationship: a post can only belong to one user but a user can have many posts. The posts table contains a `users_id` column that references the `id` column on the users table.
```php
$connection->createTable(
    'posts',
    null,
    [
        'columns' => [
            new Column('id', [
                'type'          => Column::TYPE_INTEGER,
                'size'          => 10,
                'unsigned'      => true,
                'notNull'       => true,
                'autoIncrement' => true,
            ]),
            new Column('title', [
                'type'    => Column::TYPE_VARCHAR,
                'size'    => 200,
                'notNull' => true,
            ]),
            new Column('body', [
                'type'    => Column::TYPE_TEXT,
                'notNull' => true,
            ]),
            new Column('users_id', [
                'type'     => Column::TYPE_INTEGER,
                'size'     => 10,
                'unsigned' => true,
                'notNull'  => true,
            ]),
            new Column('created_at', [
                'type'    => Column::TYPE_TIMESTAMP,
                'notNull' => true,
                'default' => 'CURRENT_TIMESTAMP',
            ]),
        ],
        'indexes' => [
            new Index('PRIMARY', ['id'], 'PRIMARY')
        ],
        'references' => [
            new Reference(
                'user_idfk',
                [
                    'referencedTable'   => 'users',
                    'columns'           => ['users_id'],
                    'referencedColumns' => ['id'],
                ]
            ),
        ],
    ]
);
```
First, we need to create factories for both users and posts:
```php
use App\Models\Posts;
use App\Models\Users;

$factory->define(Users::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'username' => $faker->userName,
        'email'    => $faker->unique()->safeEmail,
        'password' => $factory->security->hash('password'),
    ];
});

$factory->define(Posts::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->unique()->sentence(4, true),
        'body'  => $faker->paragraph(4, true),
    ];
});
``

To create three users with one post each, we could simply loop over newly created users and create a post for each, sending the user id as an attribute override:
```php
use App\Models\Posts;
use App\Models\Users;

$users = factory(Users::class, 3)->create();

foreach ($users as $user) {
    factory(Posts::class)->create([
        'users_id' => $user->id
    ]);
}
```
For multiple posts, simply pass the desired number as the second variable to the factory helper:
```php
use App\Models\Posts;
use App\Models\Users;

$users = factory(Users::class, 3)->create();

foreach ($users as $user) {
    factory(Posts::class, 3)->create([
        'users_id' => $user->id
    ]);
}
```

##### Using Named Factories
To use a name factory, pass the name as the second argument to the `factory` function:
```php
use App\Models\Users;

// Make a user using the factory named 'myUser'
factory(Users::class, 'myUser')->make()

// Create a user using the factory named 'myUser'
factory(Users::class, 'myUser')->create()
```

To make multiple instances of a named factory, pass the desired number of instances as the third argument:
```php
use App\Models\Users;

// Make three users using the factory named 'myUser'
$users = factory(Users::class, 'myUser', 3)->make();

// Create three users using the factory named 'myUser'
$users = factory(Users::class, 'myUser', 3)->creates();
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
Yarak uses Phalcon's [Database Abstraction Layer](https://docs.phalconphp.com/en/3.0.0/reference/db.html) to interact with the database. This guide will only cover the most common operations. For more detailed information about what is possible, please see the [API Documentation](https://docs.phalconphp.com/en/3.0.1/api/Phalcon_Db_Adapter.html). Because the official Phalcon migrations also use the database abstraction layer, the [Phalcon migration documentation](https://docs.phalconphp.com/en/3.0.1/reference/migrations.html#migration-class-anatomy) may also be useful.   

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
                new Index('PRIMARY', ['id'], 'PRIMARY'),
                new Index('users_username_unique', ['username'], 'UNIQUE'),
                new Index('users_email_unique', ['email'], 'UNIQUE')
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

### Calling Yarak In Code
To call a Yarak command from your codebase, use the Yarak::call static method.
```php
public static function call($command, array $arguments = [], array $config = [])
```
For example, to call `migrate --rollback --steps=2`:
```php
use Yarak\Yarak;

Yarak::call('migrate', [
    '--rollback' => true,
    '--steps'    => 2,
]);
```

Yarak will attempt to resolve its config from /app/config/service.php. If your services file is in a different location, you will need to pass the config array manually.
```php
use Yarak\Yarak;

Yarak::call('migrate', [
    '--rollback' => true,
    '--steps'    => 2,
], $configArray);
```

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/zachleigh/yarak/issues).
