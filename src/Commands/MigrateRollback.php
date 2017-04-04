<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;

class MigrateRollback extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback
                            {--steps=1 : Number of steps to rollback.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Refresh the database.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $this->getMigrator($this->getOutput())
            ->rollback($this->option('steps'));
    }
}
