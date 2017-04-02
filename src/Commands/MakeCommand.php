<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\Console\CommandCreator;
use Symfony\Component\Console\Input\InputArgument;

class MakeCommand extends YarakCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('make:command')
            ->setDescription('Create a new command file.')
            ->setHelp('This command will generate a new command file.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of your command file.');
    }

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = new CommandCreator(
            $this->getOutput()
        );

        $creator->create($this->argument('name'));
    }
}
