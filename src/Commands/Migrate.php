<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;

class Migrate extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'migrate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())->run();
    }
}
