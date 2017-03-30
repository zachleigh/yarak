<?php

namespace Yarak\Commands;

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
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())->reset();
    }
}
