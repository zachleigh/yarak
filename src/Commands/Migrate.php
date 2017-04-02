<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;

class Migrate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Run the database migrations.')
            ->setHelp('This command allows you to run migrations.');
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())->run();
    }
}
