<?php

namespace Yarak\DB;

use Yarak\Helpers\Creator;

class DirectoryCreator extends Creator
{
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
        $created = $this->makeDirectoryStructure(
            $this->config->getAllDatabaseDirectories()
        );

        foreach ($created as $key => $value) {
            $this->output->writeInfo("Created {$key} directory.");
        }
    }

    /**
     * Create factory file stub.
     */
    protected function createFactoriesFile()
    {
        $path = $this->config->getFactoryDirectory('ModelFactory.php');

        if (!file_exists($path)) {
            $stub = file_get_contents(__DIR__.'/Stubs/factory.stub');

            $this->writeFile(
                $path,
                $stub
            );

            $this->output->writeInfo('Created ModelFactory file.');
        }
    }

    /**
     * Create seeder file stub.
     */
    protected function createSeederFile()
    {
        $path = $this->config->getSeedDirectory('DatabaseSeeder.php');

        if (!file_exists($path)) {
            $stub = file_get_contents(__DIR__.'/Stubs/seeder.stub');

            $this->writeFile(
                $this->config->getSeedDirectory('DatabaseSeeder.php'),
                $stub
            );

            $this->output->writeInfo('Created DatabaseSeeder file.');
        }
    }
}
