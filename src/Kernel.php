<?php

namespace Yarak;

use Yarak\Config\Config;
use Yarak\Exceptions\InvalidInput;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class Kernel
{
    /**
     * Application config.
     *
     * @var array
     */
    private $configArray;

    /**
     * Construct.
     *
     * @param array $configArray
     */
    public function __construct(array $configArray = [])
    {
        $this->configArray = $configArray;
    }

    /**
     * Handle an incoming console command.
     */
    public function handle($input = null, $output = null)
    {
        $application = new Application('Yarak - Phalcon devtools');

        $this->registerCommands($application);

        if ($input && $output) {
            $this->validateCommand($application, $input);

            $application->setAutoExit(false);

            return $application->run($input, $output);
        }

        $application->run();
    }

    /**
     * Register all Yarak commands.
     *
     * @param Application $application
     */
    protected function registerCommands(Application $application)
    {
        $applicationCommands = $this->getApplicationCommands();

        foreach ($applicationCommands as $command) {
            $application->add(new $command());
        }
    }

    /**
     * Get array of all Yarak commands.
     *
     * @return array
     */
    protected function getApplicationCommands()
    {
        $dir = new \DirectoryIterator(__DIR__.'/Commands');

        $commands = [];

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $className = str_replace('.php', '', $fileinfo->getFilename());

                $commands[] = 'Yarak\\Commands\\'.$className;
            }
        }

        return $commands;
    }

    /**
     * Validate the given command.
     *
     * @param Application    $application
     * @param InputInterface $input
     *
     * @throws InvalidInput
     */
    protected function validateCommand(Application $application, InputInterface $input)
    {
        $command = $input->getFirstArgument();

        if ($application->has($command) === false) {
            throw InvalidInput::invalidCommand($command);
        }
    }

    /**
     * Return the Yarak config array.
     *
     * @return array
     */
    public function getConfigArray()
    {
        return $this->configArray;
    }
}
