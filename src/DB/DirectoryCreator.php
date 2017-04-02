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
        $createdDirs = (bool) count($this->makeDirectoryStructure(
            $this->config->getAllDatabaseDirectories(),
            $this->output
        ));

        $createdFactories = $this->createFactoriesFile();

        $createdSeeder = $this->createSeederFile();

        $this->outputNothingCreated(
            [$createdDirs, $createdFactories, $createdSeeder]
        );
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

            return true;
        }

        return false;
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

            return true;
        }

        return false;
    }
}
