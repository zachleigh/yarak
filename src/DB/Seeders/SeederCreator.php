<?php

namespace Yarak\DB\Seeders;

use Yarak\Helpers\Creator;

class SeederCreator extends Creator
{
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
