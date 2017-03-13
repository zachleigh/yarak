<?php

use Yarak\Kernel;
use Phalcon\Mvc\View;
use Phalcon\Security;
use Sonohini\Acl\Acl;
use Sonohini\Auth\Auth;
use Phalcon\Mvc\Dispatcher;
use Elasticsearch\ClientBuilder;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;

$di->setShared('config', function () {
    return include APP_PATH.'/config/config.php';
});

$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class,

    ]);

    return $view;
});

$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\'.$config->database->adapter;

    $params = [
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset,
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    $connection->setNestedTransactionsWithSavepoints(true);

    return $connection;
});

$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->set('flash', function () {
    return new Flash([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning',
    ]);
});

$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Sonohini\Controllers');

    return $dispatcher;
});

$di->set('security', function () {
    return new Security();
});

 $di->set('modelsManager', function() {
      return new ModelsManager();
 });

$di->setShared('yarak', function () {
    $config = $this->getConfig();

    return new Kernel([
        'application' => [
            'databaseDir' => APP_PATH.'/database/',
        ],
        'database' => [
            'adapter' => $config->database->adapter,
            'host' => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname' => $config->database->dbname,
            'charset' => $config->database->charset,
        ],
    ]);
});

