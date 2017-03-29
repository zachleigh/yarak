<?php

namespace Yarak\Commands;

use Yarak\Output\SymfonyOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateReset extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate:reset')
            ->setDescription('Rollback all migrations.')
            ->setHelp('This command allows you to rollback all database migrations.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getMigrator(new SymfonyOutput($output))->reset();
    }
}
