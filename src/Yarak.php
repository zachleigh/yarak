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
     * @param string $command
     * @param array  $arguments Argument array.
     * @param array  $config    Config values, for testing purposes.
     */
    public static function call($command, array $arguments = [], array $config = [])
    {
        if (!empty($config)) {
            $kernel = self::getKernelWithConfig($config);
        } else {
            $kernel = self::getKernel();
        }

        $arguments = ['command' => $command] + $arguments;

        $input = new ArrayInput($arguments);

        $output = new NullOutput();

        $kernel->handle($input, $output);
    }

    /**
     * Get an instance of Yarak kernel built with the given config.
     *
     * @param array $config
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
     * @throws FileNotFound
     *
     * @return Kernel
     */
    protected static function getKernel()
    {
        $di = new FactoryDefault();

        $servicesPath = __DIR__.'/../../../../app/config/services.php';

        try {
            include $servicesPath;
        } catch (\Exception $e) {
            throw FileNotFound::servicesFileNotFound(
                'Try passing the Yarak config array as the third argument '.
                'to Yarak::call.'
            );
        }

        $di->getConfig();

        return $di->get('yarak');
    }
}
