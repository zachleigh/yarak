<?php

namespace Yarak;

use Yarak\Commands\Migrate;
use Yarak\Commands\DBGenerate;
use Phalcon\Di\FactoryDefault;
use Yarak\Commands\MakeMigration;
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
    private $config;

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
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
        $application->add(new DBGenerate($this->config));
        $application->add(new MakeMigration($this->config));
        $application->add(new Migrate($this->config));
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
    public function getConfig()
    {
        return $this->config;
    }
}
