<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\DB\Seeders\SeedRunner;
use Symfony\Component\Console\Input\InputArgument;

class DBSeed extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('db:seed')
            ->setDescription('Seed the database.')
            ->setHelp('This command will run the given seeder class.')
            ->addArgument(
                'class',
                InputArgument::OPTIONAL,
                'The name of the seeder class to run.',
                'DatabaseSeeder'
            );
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $seedRunner = new SeedRunner($this->getOutput());

        $seedRunner->run($this->argument('class'));
    }
}
