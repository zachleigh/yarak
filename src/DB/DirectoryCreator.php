<?php

namespace Yarak\DB;

use Yarak\Config\Config;
use Yarak\Helpers\Paths;
use Yarak\Helpers\Loggable;

class DirectoryCreator
{
    use Loggable, Paths;

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

        $this->log("<info>Created all directories.</info>");
    }

    /**
     * Create factory file stub.
     */
    protected function createFactoriesFile()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/factory.stub');

        try {
            file_put_contents(
                $this->config->getFactoryDirectory('ModelFactory.php'),
                $stub
            );
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        $this->log("<info>Created ModelFactory file.</info>");
    }

    /**
     * Create seeder file stub.
     */
    protected function createSeederFile()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/seeder.stub');

        try {
            file_put_contents(
                $this->config->getSeedDirectory('DatabaseSeeder.php'),
                $stub
            );
        } catch (\Exception $e) {
            throw new Exception($e);
        }

        $this->log("<info>Created DatabaseSeeder file.</info>");
    }
}
