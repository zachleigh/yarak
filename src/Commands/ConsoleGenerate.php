<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\Console\YarakCommand;
use Yarak\Console\DirectoryCreator;

class ConsoleGenerate extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('console:generate')
            ->setDescription('Generate the console directory structure.')
            ->setHelp(
                'This command will create all the console directories and files necessary for the Yarak console componet to run.'
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
