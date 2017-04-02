<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class DBGenerateTest extends \Codeception\Test\Unit
{
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
    public function db_generate_command_makes_all_directories_and_files()
    {
        $this->tester->removeDatabaseDirectory();

        $paths = $this->getPaths();

        $this->tester->assertAllPathsDontExist($paths);

        Yarak::call('db:generate', [], DI::getDefault());

        $this->tester->assertAllPathsExist($paths);
    }

    /**
     * Set all paths to be created.
     */
    protected function getPaths()
    {
        $config = $this->tester->getConfig();

        return [
            $config->getMigrationDirectory(),
            $config->getSeedDirectory(),
            $config->getFactoryDirectory(),
            $config->getFactoryDirectory('ModelFactory.php'),
            $config->getSeedDirectory('DatabaseSeeder.php'),
        ];
    }
}
