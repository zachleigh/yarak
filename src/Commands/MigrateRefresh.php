<?php

namespace Yarak\Commands;

use Yarak\Output\SymfonyOutput;
use Yarak\DB\Seeders\SeedRunner;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyOutput = new SymfonyOutput($output);

        $this->getMigrator($symfonyOutput)->refresh();

        if ($input->getOption('seed')) {
            $seedRunner = new SeedRunner($symfonyOutput);

            $seedRunner->run($input->getOption('class'));
        }
    }
}
