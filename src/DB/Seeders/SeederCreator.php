<?php

namespace Yarak\DB\Seeders;

use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;

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
     * Construct.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
