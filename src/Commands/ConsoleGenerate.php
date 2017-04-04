<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\Console\DirectoryCreator;

class ConsoleGenerate extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'console:generate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate the console directory structure.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = new DirectoryCreator(
            $this->getOutput()
        );

        $creator->create();
    }
}
