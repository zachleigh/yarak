<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\DB\DirectoryCreator;
use Yarak\Console\YarakCommand;

class DBGenerate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('db:generate')
            ->setDescription('Generate the database directory structure.')
            ->setHelp(
                'This command will create all the database directories and files necessary for yarak to run.'
            );
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = new DirectoryCreator(
            Config::getInstance($this->configArray),
            $this->getOutput()
        );

        $creator->create();
    }
}
