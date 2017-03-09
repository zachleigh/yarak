<?php

namespace Yarak\DB;

use Yarak\Config\Config;
use Yarak\Helpers\Loggable;
use Yarak\Helpers\Filesystem;

class DirectoryCreator
{
    use Filesystem, Loggable;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Construct.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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

        $this->log('<info>Created all directories.</info>');
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

        $this->log('<info>Created ModelFactory file.</info>');
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

        $this->log('<info>Created DatabaseSeeder file.</info>');
    }
}
