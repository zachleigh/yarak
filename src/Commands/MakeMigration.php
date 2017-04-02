<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\Console\YarakCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeMigration extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('make:migration')
            ->setDescription('Create a new migration file.')
            ->setHelp('This command allows you to make migration files.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of your migration, words separated by underscores.')
            ->addOption(
                'create',
                'c',
                InputOption::VALUE_REQUIRED,
                'The name of the table to create.'
            );
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = $this->getCreator();

        $create = is_null($create = $this->option('create')) ? false : $create;

        $creator->create($this->argument('name'), $create);
    }

    /**
     * Get a the migration creator class.
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    protected function getCreator()
    {
        $config = Config::getInstance($this->configArray);

        $migratorType = ucfirst($config->get('migratorType'));

        $name = "Yarak\\Migrations\\{$migratorType}\\{$migratorType}MigrationCreator";

        return new $name($config, $this->getOutput());
    }
}
