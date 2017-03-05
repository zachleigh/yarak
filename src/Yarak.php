<?php

namespace Yarak;

use Phalcon\Di\FactoryDefault;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Yarak
{
    /**
     * Call a Yarak console command.
     *
     * @param array $arguments Argument array.
     * @param array $config    Config values, for testing purposes.
     */
    public static function call(array $arguments, array $config = [])
    {
        if (!empty($config)) {
            $kernel = self::getKernelWithConfig($config);
        } else {
            $kernel = self::getKernel();
        }

        $input = new ArrayInput($arguments);

        $output = new NullOutput();

        $kernel->handle($input, $output);
    }

    /**
     * Get an instance of Yarak kernel built with the given config.
     *
     * @param  array  $config
     *
     * @return Kernel
     */
    protected static function getKernelWithConfig(array $config)
    {
        return new Kernel($config);
    }

    /**
     * Resolve Yarak kernel from di.
     *
     * @throws   
     *
     * @return Kernel
     */
    protected static function getKernel()
    {
        $di = new FactoryDefault();

        $configPath = __DIR__.'/../../../../app/config/';

        try {
            include $configPath.'services.php';
            
            $di->getConfig();

            return $di->get('yarak');
        } catch (\Exception $e) {
            // custom exception
            throw new \Exception(
                'Unable to resolve yarak. '.
                'Try passing yarak config array as third argument'
            );
        }
    }
}
