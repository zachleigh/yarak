<?php

namespace Yarak\DB\Seeders;

use Yarak\Console\Output\Output;

abstract class Seeder
{
    /**
     * SeedRunner instance.
     *
     * @var SeedRunner
     */
    protected $runner;

    /**
     * Output strategy.
     *
     * @var Output
     */
    protected $output;

    /**
     * Run the database seed logic.
     */
    abstract public function run();

    /**
     * Call the run method on the given seeder class.
     *
     * @param string $class
     */
    protected function call($class)
    {
        $this->runner->run($class);
    }

    /**
     * Set SeedRunner instance on object.
     *
     * @param SeedRunner $runner
     *
     * @return $this
     */
    public function setRunner(SeedRunner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Set output strategy on object.
     *
     * @param Output $output
     *
     * @return $this
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;

        return $this;
    }
}
