<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\DB\Seeders\SeedRunner;
use Symfony\Component\Console\Input\InputOption;

class MigrateRefresh extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate:refresh')
            ->setDescription('Refresh the database.')
            ->setHelp(
                'This command allows you to refresh the database by rolling back and re-running all migrations.'
            )
            ->addSeed()
            ->addSeedClass();
    }

    /**
     * Add seed option.
     */
    protected function addSeed()
    {
        return $this->addOption(
            'seed',
            null,
            InputOption::VALUE_NONE,
            'Seed the database after refreshing.'
        );
    }

    /**
     * Add seed class option.
     */
    protected function addSeedClass()
    {
        return $this->addOption(
            'class',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the seeder class to run.',
            'DatabaseSeeder'
        );
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())->refresh();

        if ($this->option('seed')) {
            $seedRunner = new SeedRunner($this->getOutput());

            $seedRunner->run($this->option('class'));
        }
    }
}
