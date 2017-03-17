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
        $this->runner->run($class);
    }

    public function setRunner(SeedRunner $runner)
    {
        $this->runner = $runner;
    }
}
