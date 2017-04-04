<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\Console\CommandCreator;

class MakeCommand extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:command
                            {name : The name of your command file.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new command file.';

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
