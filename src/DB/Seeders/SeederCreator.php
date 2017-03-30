<?php

namespace Yarak\DB\Seeders;

use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;
use Yarak\Console\Output\Output;

class SeederCreator
{
    use Filesystem;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Output strategy.
     *
     * @var Output
     */
    protected $output;

    /**
     * Construct.
     *
     * @param Config $config
     * @param Output $output
     */
    public function __construct(Config $config, Output $output)
    {
        $this->config = $config;
        $this->output = $output;
    }

    /**
     * Create a seeder file with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    public function create($name)
    {
        $seedDir = $this->config->getSeedDirectory();

        $this->makeDirectoryStructure([
            $this->config->getDatabaseDirectory(),
            $seedDir,
        ]);

        $this->writeFile(
            $path = $seedDir.$name.'.php',
            $this->getStub($name)
        );

        $this->output->writeInfo("Successfully created seeder {$name}.");

        return $path;
    }

    /**
     * Get the stub and insert the given class name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getStub($name)
    {
        $stub = file_get_contents(__DIR__.'/../Stubs/seeder.stub');

        return str_replace('DatabaseSeeder', $name, $stub);
    }
}
