<?php

namespace Yarak;

use Yarak\Commands\Migrate;
use Yarak\Commands\MakeMigration;
use Symfony\Component\Console\Application;

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
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming console command.
     */
    public function handle($input = null, $output = null)
    {
        $application = new Application();

        $this->registerCommands($application);

        if ($input && $output) {
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
        $application->add(new MakeMigration($this->config));
        $application->add(new Migrate($this->config));
    }
}
