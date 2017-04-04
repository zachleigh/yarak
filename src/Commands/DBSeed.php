<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\DB\Seeders\SeedRunner;

class DBSeed extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'db:seed
                            {class=DatabaseSeeder : The name of the seeder class to run.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Seed the database.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $seedRunner = new SeedRunner($this->getOutput());

        $seedRunner->run($this->argument('class'));
    }
}
