<?php

namespace Yarak\Commands;

use Symfony\Component\Console\Input\InputOption;

class MigrateRollback extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate:rollback')
            ->setDescription('Rollback migrations by given number of steps.')
            ->setHelp('This command allows you to rollback migrations.')
            ->addOption(
                'steps',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of steps to rollback.',
                1
            );
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())
            ->rollback($this->option('steps'));
    }
}
