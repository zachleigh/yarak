<?php

namespace Yarak;

use Yarak\Kernel;
use Phalcon\Di\FactoryDefault;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Yarak
{
    /**
     * Call a Yarak console command.
     *
     * @param  array  $arguments Argument array.
     * @param  array  $config    Config values, for testing purposes.
     */
    public static function call(array $arguments, array $config = [])
    {
        $di = new FactoryDefault();

        try {
            $kernel = $di->get('yarak');
        } catch (\Exception $e) {
            $kernel = new Kernel($config);
        }

        $input = new ArrayInput($arguments);

        $output = new NullOutput();

        $kernel->handle($input, $output);
    }
}
