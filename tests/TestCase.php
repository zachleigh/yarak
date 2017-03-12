<?php

namespace Yarak\Tests;

use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;
use Yarak\DB\DirectoryCreator;
use Yarak\Migrations\Migrator;
use Yarak\DB\ConnectionResolver;
use Yarak\Migrations\MigrationCreator;
use Yarak\Tests\Concerns\DatabaseConcerns;
use Yarak\Migrations\CreateMigrationsTable;
use Yarak\Migrations\DatabaseMigrationRepository;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class TestCase extends \PHPUnit_Framework_TestCase
{
    use Filesystem, DatabaseConcerns;

    /**
     * Symfony filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Tables used for testing.
     *
     * @var array
     */
    protected $tables = [
        'migrations',
        'posts',
        'users',
    ];

    /**
     * Connection to database.
     *
     * @var Phalcon\Db\Adapter\Pdo
     */
    private $connection = null;

    /**
     * Set up the class before a test.
     */
    public function setUp()
    {
        parent::setUp();

        $connection = $this->getConnection();

        foreach ($this->tables as $table) {
            $connection->dropTable($table);
        }

        $this->filesystem = new SymfonyFilesystem();

        $databaseDir = $this->getConfig()->getDatabaseDirectory();

        $this->filesystem->remove($databaseDir);

        $this->filesystem->remove(__DIR__.'/app/models');
    }

    /**
     * Return a config array.
     *
     * @return array
     */
    protected function getConfig()
    {
        $configArray = [
            'application' => [
                'databaseDir' => __DIR__.'/app/database/',
            ],
            'database' => [
                'adapter'  => 'Mysql',
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => 'password',
                'dbname'   => 'yarak',
                'charset'  => 'utf8',
            ],
        ];

        return Config::getInstance($configArray);
    }

    /**
     * Get an instance of MigrationCreator.
     *
     * @return MigrationCreator
     */
    protected function getMigrationCreator()
    {
        $config = $this->getConfig();

        return new MigrationCreator($config);
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
     * Clear the migration table.
     *
     * @return Migrator
     */
    protected function clearMigrationTable()
    {
        $connection = $this->getConnection();

        if ($connection->tableExists('migrations')) {
            $migration = new CreateMigrationsTable();

            $migration->down($connection);
        }
    }

    /**
     * Gett the migrator.
     *
     * @return Migrator
     */
    protected function getMigrator()
    {
        $config = $this->getConfig();

        $resolver = new ConnectionResolver();

        $repository = new DatabaseMigrationRepository();

        return new Migrator($config, $resolver, $repository);
    }

    /**
     * Return a database connection.
     *
     * @return Phalcon\Db\Adapter\Pdo
     */
    protected function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }

        $dbConfig = $this->getConfig()->get('database');

        $resolver = new ConnectionResolver();

        return $this->connection = $resolver->getConnection($dbConfig);
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
        $this->createMigration();

        $migrator->run();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $migrator->run();
    }
}
