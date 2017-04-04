<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;
use Yarak\DB\Seeders\SeederCreator;

class MakeSeeder extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:seeder
                            {name : The name of your seeder file.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder file.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = new SeederCreator(
            $this->getOutput()
        );

        $creator->create($this->argument('name'));
    }
}
