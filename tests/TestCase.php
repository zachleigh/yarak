<?php

namespace Yarak\Tests;

use Phalcon\Di;
use Yarak\Yarak;
use Yarak\Config\Config;
use Sonohini\Models\Roles;
use Yarak\Helpers\Filesystem;
use Phalcon\Di\FactoryDefault;
use Yarak\DB\DirectoryCreator;
use Yarak\DB\ConnectionResolver;
use Yarak\Migrations\FileDate\FileDateMigrator;
use Yarak\Migrations\FileDate\FileDateMigrationCreator;
use Yarak\Migrations\Repositories\DatabaseMigrationRepository;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class TestCase extends \Codeception\Test\Unit
{
    use Filesystem;

    /**
     * @var bool
     */
    private $_loaded = false;

    /**
     * Setup the test case.
     */
    public function setUp()
    {
        parent::setUp();

        $this->di = new FactoryDefault();

        $this->filesystem = new SymfonyFilesystem();

        // $databaseDir = Config::getInstance()->getDatabaseDirectory();

        // $this->filesystem->remove($databaseDir);

        // $this->filesystem->remove(__DIR__.'/../app/models');

        $this->_loaded = true;
    }

    /**
     * Return a config array.
     *
     * @return array
     */
    public function getConfig()
    {
        return Config::getInstance();
    }

    protected function removeDatabaseDirectory()
    {
        $migrationDir = Config::getInstance()->getDatabaseDirectory();

        $this->filesystem->remove($migrationDir);
    }

    protected function removeMigrationDirectory()
    {
        $migrationDir = Config::getInstance()->getMigrationDirectory();

        $this->filesystem->remove($migrationDir);
    }

    protected function removeSeedDirectory()
    {
        $seedDir = Config::getInstance()->getSeedDirectory();

        $this->filesystem->remove($seedDir);
    }

    protected function removeFactoryDirectory()
    {
        $factoryDir = Config::getInstance()->getFactoryDirectory();

        $this->filesystem->remove($factoryDir);
    }

    /**
     * Get an instance of DirectoryCreator.
     *
     * @return DirectoryCreator
     */
    protected function getDirectoryCreator()
    {
        $config = $this->getConfig();
        
        return new DirectoryCreator($config);
    }

    /**
     * Create a migration file.
     *
     * @param string $name
     *
     * @return string
     */
    protected function createMigration($name = '2017_01_01_000001_create_users_table.php')
    {
        $file = file_get_contents(__DIR__.'/Migrations/'.$name);

        $directories = $this->getConfig()->getAllDatabaseDirectories();

        $this->makeDirectoryStructure($directories);

        $path = $this->getConfig()->getMigrationDirectory().$name;

        $this->writeFile($path, $file);

        return $path;
    }

    /**
     * Get the migrator.
     *
     * @return Yarak\Migrations\Migrator
     */
    protected function getMigrator($type = 'fileDate')
    {
        $config = $this->getConfig();

        $resolver = new ConnectionResolver();

        $repository = new DatabaseMigrationRepository();

        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrator($config, $resolver, $repository);
        }
    }

    /**
     * Get an instance of the migration creator.
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    protected function getMigrationCreator($type = 'fileDate')
    {
        $config = $this->getConfig();

        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrationCreator($config);
        }
    }

    /**
     * Create multiple migrations in a single step.
     *
     * @param Yarak\Migrations\Migrator $migrator
     */
    protected function createSingleStep($migrator)
    {
        $this->createMigration();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $migrator->run();
    }

    /**
     * Create multiple migrations in two steps.
     *
     * @param Yarak\Migrations\Migrator $migrator
     */
    protected function createTwoSteps($migrator)
    {
        $this->removeMigrationDirectory();
        
        $this->createMigration();

        $migrator->run();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $migrator->run();
    }

    /**
     * Get a file name from a path.
     *
     * @param string $path
     * @param string $extension
     *
     * @return string
     */
    protected function getFileNameFromPath($path, $extension = '.php')
    {
        $pathArray = explode('/', $path);

        return str_replace($extension, '', array_pop($pathArray));
    }
}
