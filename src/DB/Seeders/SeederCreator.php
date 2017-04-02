<?php

namespace Yarak\DB\Seeders;

use Yarak\Helpers\Creator;
use Yarak\Exceptions\WriteError;

class SeederCreator extends Creator
{
    /**
     * Create a seeder file with the given name.
     *
     * @param string $name
     *
     * @throws WriteError
     *
     * @return string
     */
    public function create($name)
    {
        $path = $this->config->getSeedDirectory($name.'.php');

        if (!file_exists($path)) {
            $this->makeDirectoryStructure([
                'database' => $this->config->getDatabaseDirectory(),
                'seeds'    => $this->config->getSeedDirectory(),
            ], $this->output);

            $this->writeFile($path, $this->getStub($name));

            $this->output->writeInfo("Created seeder {$name}.");

            return $path;
        }

        throw WriteError::commandExists($name);
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
