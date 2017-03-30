<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\DB\Seeders\SeederCreator;
use Symfony\Component\Console\Input\InputArgument;

class MakeSeeder extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('make:seeder')
            ->setDescription('Create a new seeder file.')
            ->setHelp('This command will generate a new seeder file.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of your seeder file.');
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = new SeederCreator(
            Config::getInstance($this->configArray),
            $this->getOutput()
        );

        $creator->create($this->argument('name'));
    }
}
