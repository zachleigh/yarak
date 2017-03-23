<?php

namespace Yarak\DB;

use Yarak\Config\Config;
use Yarak\Output\Output;
use Yarak\Helpers\Filesystem;

class DirectoryCreator
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
     * Create all the directories and files necessary for Yarak DB functions.
     */
    public function create()
    {
        $this->createAllDirectories();

        $this->createFactoriesFile();

        $this->createSeederFile();
    }

    /**
     * Create all needed directories.
     */
    protected function createAllDirectories()
    {
        $this->makeDirectoryStructure(
            $this->config->getAllDatabaseDirectories()
        );

        $this->output->writeInfo('Created migrations directory.');

        $this->output->writeInfo('Created seeds directory.');

        $this->output->writeInfo('Created factories directory.');
    }

    /**
     * Create factory file stub.
     */
    protected function createFactoriesFile()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/factory.stub');

        $this->writeFile(
            $this->config->getFactoryDirectory('ModelFactory.php'),
            $stub
        );

        $this->output->writeInfo('Created ModelFactory file.');
    }

    /**
     * Create seeder file stub.
     */
    protected function createSeederFile()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/seeder.stub');

        $this->writeFile(
            $this->config->getSeedDirectory('DatabaseSeeder.php'),
            $stub
        );

        $this->output->writeInfo('Created DatabaseSeeder file.');
    }
}
