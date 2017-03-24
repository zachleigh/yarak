<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\Output\SymfonyOutput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $create = is_null($create = $input->getOption('create')) ? false : $create;

        $config = Config::getInstance($this->configArray);

        $creator = $this->getCreator($config, $output);

        $creator->create($name, $create);
    }

    /**
     * Get a the migration creator class.
     *
     * @param Config          $config
     * @param OutputInterface $output
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    protected function getCreator(Config $config, OutputInterface $output)
    {
        $symfonyOutput = new SymfonyOutput($output);

        $migratorType = ucfirst($config->get('migratorType'));

        $name = "Yarak\\Migrations\\{$migratorType}\\{$migratorType}MigrationCreator";

        return new $name($config, $symfonyOutput);
    }
}
