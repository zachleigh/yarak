<?php

namespace Yarak\Commands;

use Yarak\DB\DirectoryCreator;
use Yarak\Console\YarakCommand;

class DBGenerate extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'db:generate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate the database directory structure.';

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
