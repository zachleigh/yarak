<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\DB\Seeders\SeedRunner;

class MigrateRefresh extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'migrate:refresh
                            {--seed : Seed the database after refreshing.}
                            {--class=DatabaseSeeder : The name of the seeder class to run.}';

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
        $this->getMigrator($this->getOutput())->refresh();

        if ($this->option('seed')) {
            $seedRunner = new SeedRunner($this->getOutput());

            $seedRunner->run($this->option('class'));
        }
    }
}
