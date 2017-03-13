<?php

namespace Yarak\Tests\Functional;

use Yarak\Yarak;
use Yarak\Config\Config;
use Yarak\Tests\TestCase;

class DBGenerateTest extends TestCase
{
    /**
     * Array of paths that should be created.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @test
     */
    public function it_makes_all_directories_and_files()
    {
        $this->removeDatabaseDirectory();
        
        $config = $this->getConfig();

        $this->setPaths($config);

        $this->assertAllPathsDontExist();

        Yarak::call('db:generate', [], $config->getAll());

        $this->assertAllPathsExist();
    }

    /**
     * Set all paths to be created.
     *
     * @param Config $config
     */
    protected function setPaths(Config $config)
    {
        $this->paths = [
            $config->getMigrationDirectory(),
            $config->getSeedDirectory(),
            $config->getFactoryDirectory(),
            $config->getFactoryDirectory('ModelFactory.php'),
            $config->getSeedDirectory('DatabaseSeeder.php'),
        ];
    }

    /**
     * Assert all paths in this->paths don't exist.
     */
    protected function assertAllPathsDontExist()
    {
        foreach ($this->paths as $path) {
            $this->assertFileNotExists($path);
        }
    }

    /**
     * Assert all paths in this->paths exist.
     */
    protected function assertAllPathsExist()
    {
        foreach ($this->paths as $path) {
            $this->assertFileExists($path);
        }
    }
}
