<?php

namespace Yarak;

use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault;
use Yarak\Exceptions\FileNotFound;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class Yarak
{
    /**
     * Call a Yarak console command.
     *
     * @param string         $command
     * @param array          $arguments Argument array.
     * @param FactoryDefault $di        DI, may be necessary for php 5.6.
     * @param bool           $debug     If true, use and return buffered output.
     */
    public static function call(
        $command,
        array $arguments = [],
        DiInterface $di = null,
        $debug = false
    ) {
        $kernel = self::getKernel($di);

        $arguments = ['command' => $command] + $arguments;

        $input = new ArrayInput($arguments);

        if ($debug) {
            $output = new BufferedOutput();

            $kernel->handle($input, $output);

            return $output;
        }

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
