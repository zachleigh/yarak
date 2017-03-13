<?php
/**
 * The app directory is here for testing purposes only.
 * This file bootstraps the Codeception test suite.
 */

use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;

$config = include __DIR__ . '/config.php';

include __DIR__ . '/loader.php';

$di = new FactoryDefault();

include __DIR__ . '/services.php';

return new Application();
