<?php

namespace Helper;

use Phalcon\Di;
use Yarak\Yarak;
use Faker\Factory;
use Yarak\Config\Config;
use Phalcon\Di\FactoryDefault;

class Main extends \Codeception\Module
{
    /**
     * Di instance.
     *
     * @var FactoryDefault
     */
    protected $di;

    /**
     * Setup the test case.
     */
    public function setUp()
    {
        $this->di = new FactoryDefault();

        $this->getModule('\Helper\Filesystem')->setFilesystem();

        $this->getModule('\Helper\Database')->resetDatabase();
    }

    /**
     * Setup factory test class.
     */
    public function factorySetUp()
    {
        $this->setUp();

        $config = Config::getInstance();

        $this->getModule('\Helper\Filesystem')->createAllPaths($config);

        $this->getModule('\Helper\Filesystem')->copyStubs($config);

        $this->createSingleStep();
    }

    /**
     * Setup seeder test class.
     */
    public function seederSetUp()
    {
        $this->factorySetUp();

        $this->getModule('\Helper\Filesystem')->copySeeders();
    }

    /**
     * Create a migration file.
     *
     * @param string $name
     *
     * @return string
     */
    public function createMigration($name = '2017_01_01_000001_create_users_table.php')
    {
        $config = Config::getInstance();

        $directories = $config->getAllDatabaseDirectories();

        $this->getModule('\Helper\Filesystem')->makeDirectoryStructure($directories);

        $this->getModule('\Helper\Filesystem')->writeFile(
            $path = $config->getMigrationDirectory().$name,
            file_get_contents(__DIR__.'/../../_data/Migrations/'.$name)
        );

        return $path;
    }

    /**
     * Create multiple migrations in a single step.
     *
     * @param Yarak\Migrations\Migrator $migrator
     */
    public function createSingleStep($migrator = null)
    {
        if ($migrator === null) {
            $migrator = $this->getModule('\Helper\Builder')->getMigrator();
        }

        $this->createMigration();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $migrator->run();
    }

    /**
     * Create multiple migrations in two steps.
     *
     * @param Yarak\Migrations\Migrator $migrator
     */
    public function createTwoSteps($migrator = null)
    {
        if ($migrator === null) {
            $migrator = $this->getModule('\Helper\Builder')->getMigrator();
        }

        $this->getModule('\Helper\Filesystem')->removeMigrationDirectory();

        $this->createMigration();

        $migrator->run();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $migrator->run();
    }
}
