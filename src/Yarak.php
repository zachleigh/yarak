<?php

namespace Yarak;

use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault;
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
        $di = is_null($di) ? DI::getDefault() : $di;

        $kernel = $di->get('yarak');

        $input = new ArrayInput(['command' => $command] + $arguments);

        if ($debug) {
            $kernel->handle($input, $output = new BufferedOutput());

            return $output;
        }

        $kernel->handle($input, new NullOutput());
    }
}
