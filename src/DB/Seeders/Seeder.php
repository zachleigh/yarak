<?php

namespace Yarak\DB\Seeders;

abstract class Seeder
{
    /**
     * Run the database seed logic.
     */
    abstract public function run();

    /**
     * Call the run method on the given seeder class.
     *
     * @param  string $class
     */
    protected function call($class)
    {
        $seedRunner = new SeedRunner();

        $seedRunner->run($class);
    }
}
