<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class DBGenerateTest extends \Codeception\Test\Unit
{
    /**
     * Array of paths that should be created.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();
    }

    /**
     * @test
     */
    public function it_makes_all_directories_and_files()
    {
        $this->tester->removeDatabaseDirectory();

        $this->setPaths();

        $this->assertAllPathsDontExist();

        Yarak::call('db:generate', [], DI::getDefault());

        $this->assertAllPathsExist();
    }

    /**
     * Set all paths to be created.
     */
    protected function setPaths()
    {
        $config = $this->tester->getConfig();

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
