<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\DB\ConnectionResolver;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Run the database migrations.')
            ->setHelp('This command allows you to run migrations.')
            ->addRollback()
            ->addSteps()
            ->addReset()
            ->addRefresh();
    }

    /**
     * Add rollback option.
     */
    protected function addRollback()
    {
        return $this->addOption(
            'rollback',
            null,
            InputOption::VALUE_NONE,
            'Rollback migrations by given number of steps.'
        );
    }

    /**
     * Add steps option.
     */
    protected function addSteps()
    {
        return $this->addOption(
            'steps',
            null,
            InputOption::VALUE_OPTIONAL,
            'Number of steps to rollback.',
            1
        );
    }

    /**
     * Add reset option.
     */
    protected function addReset()
    {
        return $this->addOption(
            'reset',
            null,
            InputOption::VALUE_NONE,
            'Rollback all migrations.'
        );
    }

    /**
     * Add refresh option.
     */
    protected function addRefresh()
    {
        return $this->addOption(
            'refresh',
            null,
            InputOption::VALUE_NONE,
            'Rollback and re-run all migrations.'
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
        $migrator = $this->getMigrator();

        if ($input->getOption('rollback')) {
            $migrator->rollback($input->getOption('steps'));
        } elseif ($input->getOption('reset')) {
            $migrator->reset();
        } elseif ($input->getOption('refresh')) {
            $migrator->refresh();
        } else {
            $migrator->run();
        }

        foreach ($migrator->getLog() as $message) {
            $output->writeln($message);
        }
    }

    /**
     * Get an instance of the migrator.
     *
     * @return Migrator
     */
    protected function getMigrator()
    {
        $config = Config::getInstance($this->configArray);

        $migratorClassName = $this->getMigratorClassName($config);

        return new $migratorClassName(
            $config,
            new ConnectionResolver(),
            $this->getRepository($config)
        );
    }

    /**
     * Get the name of the migrator class.
     *
     * @param Config $config
     *
     * @return string
     */
    protected function getMigratorClassName(Config $config)
    {
        $migratorType = ucfirst($config->get('migratorType'));

        return "Yarak\\Migrations\\$migratorType\\".
            $migratorType.'Migrator';
    }

    /**
     * Get an instance of MigrationRepository.
     *
     * @param Config $config
     *
     * @return Yarak\Migrations\MigrationRepository
     */
    protected function getRepository(Config $config)
    {
        $repositoryType = ucfirst($config->get('migrationRepository'));

        $repositoryClass = 'Yarak\\Migrations\\Repositories\\'.
            $repositoryType.'MigrationRepository';

        return new $repositoryClass();
    }
}
