<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;

class MigrateReset extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'migrate:reset';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Rollback all migrations.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())->reset();
    }
}
