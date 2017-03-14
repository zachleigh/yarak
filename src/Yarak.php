<?php

namespace Yarak;

use Phalcon\Di\FactoryDefault;
use Yarak\Exceptions\FileNotFound;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Yarak
{
    /**
     * Call a Yarak console command.
     *
     * @param string         $command
     * @param array          $arguments Argument array.
     * @param FactoryDefault $di        DI, may be necessary for php 5.6.
     */
    public static function call($command, array $arguments = [], FactoryDefault $di = null)
    {
        $kernel = self::getKernel($di);

        $arguments = ['command' => $command] + $arguments;

        $input = new ArrayInput($arguments);

        $output = new NullOutput();

        $kernel->handle($input, $output);
    }

    /**
     * Resolve Yarak kernel from di.
     *
     * @param FactoryDefault|null $di
     *
     * @return Kernel
     */
    protected static function getKernel($di)
    {
        if ($di === null) {
            $di = self::getDI();
        }

        return $di->get('yarak');
    }

    /**
     * Get a fresh DI instance.
     *
     * @throws FileNotFound
     *
     * @return FactoryDefault
     */
    protected static function getDI()
    {
        $di = new FactoryDefault();

        $servicesPath = __DIR__.'/../../../../app/config/services.php';

        if (!realpath($servicesPath)) {
            $servicesPath = __DIR__.'/../app/config/services.php';
        }

        try {
            include $servicesPath;
        } catch (\Exception $e) {
            throw FileNotFound::servicesFileNotFound(
                'Try passing the Yarak config array as the third argument '.
                'to Yarak::call.'
            );
        }

        return $di;
    }
}
