<?php

namespace Helper;

use Phalcon\Di;
use Yarak\Yarak;
use Faker\Factory;
use App\Models\Users;
use Codeception\Actor;
use Yarak\Config\Config;
use Phalcon\Di\FactoryDefault;
use Yarak\DB\DirectoryCreator;
use Yarak\DB\ConnectionResolver;
use Yarak\DB\Factories\ModelFactory;
use Symfony\Component\Filesystem\Filesystem;
use Yarak\Migrations\FileDate\FileDateMigrator;
use Yarak\Migrations\FileDate\FileDateMigrationCreator;
use Yarak\Migrations\Repositories\DatabaseMigrationRepository;

class All extends \Codeception\Module
{
    /**
     * Database connection.
     *
     * @var Phalcon\Db\Adapter\Pdo
     */
    protected $connection;

    /**
     * Symfony filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Setup the test case.
     */
    public function setUp()
    {
        $this->di = new FactoryDefault();

        $this->filesystem = new Filesystem();

        $this->resetDatabase();
    }

    /**
     * Clear all database tables.
     */
    protected function resetDatabase()
    {
        $migrator = $this->getMigrator()->setConnection();

        $connection = $migrator->getConnection();

        $connection->dropTable('posts');

        $connection->dropTable('users');

        $connection->dropTable('migrations');

        $this->removeDatabaseDirectory();
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

    /**
     * Remove test app database directory.
     */
    public function removeDatabaseDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getDatabaseDirectory());
    }

    /**
     * Remove test app migration directory.
     */
    public function removeMigrationDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getMigrationDirectory());
    }

    /**
     * Remove test app seed directory.
     */
    public function removeSeedDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getSeedDirectory());
    }

    /**
     * Remove test app factory directory.
     */
    public function removeFactoryDirectory()
    {
        $this->filesystem->remove(Config::getInstance()->getFactoryDirectory());
    }

    /**
     * Return symfony filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get an instance of DirectoryCreator.
     *
     * @return DirectoryCreator
     */
    public function getDirectoryCreator()
    {
        return new DirectoryCreator($this->getConfig());
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
        $file = file_get_contents(__DIR__.'/../../_data/Migrations/'.$name);

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
    public function getMigrator($type = 'fileDate')
    {
        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrator(
                $this->getConfig(),
                new ConnectionResolver(),
                new DatabaseMigrationRepository()
            );
        }
    }

    /**
     * Get an instance of the migration creator.
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    public function getMigrationCreator($type = 'fileDate')
    {
        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrationCreator($this->getConfig());
        }
    }

    /**
     * Create multiple migrations in a single step.
     *
     * @param Yarak\Migrations\Migrator $migrator
     */
    public function createSingleStep($migrator = null)
    {
        if ($migrator === null) {
            $migrator = $this->getMigrator();
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
            $migrator = $this->getMigrator();
        }

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
    public function getFileNameFromPath($path, $extension = '.php')
    {
        $pathArray = explode('/', $path);

        return str_replace($extension, '', array_pop($pathArray));
    }

    /**
     * Setup the class.
     */
    public function factorySetUp()
    {
        $this->setUp();

        $config = Config::getInstance();

        $this->createAllPaths($config);

        $this->copyStubs($config);

        $this->createMigration();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        $this->getMigrator()->run();
    }

    /**
     * Assert that given user is instance of Users and properties are set.
     *
     * @param Users $user
     */
    public function assertUserInstanceMade(Users $user)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertTrue(is_string($user->username));

        $this->assertTrue(is_string($user->email));

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Assert that given user is instance of user and saved in database.
     *
     * @param Users $user
     * @param Actor $tester
     */
    public function assertUserInstanceCreated(Users $user, Actor $tester)
    {
        $this->assertInstanceOf(Users::class, $user);

        $tester->seeRecord(Users::class, [
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }

    /**
     * Assert that user object has given attributes.
     *
     * @param Users $user
     * @param array $attributes
     */
    public function assertUserHasAttributes(Users $user, array $attributes)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals($attributes['username'], $user->username);

        $this->assertEquals($attributes['email'], $user->email);

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Create all paths necessary for seeding.
     *
     * @param Config $config
     */
    public function createAllPaths(Config $config)
    {
        $directories = $config->getAllDatabaseDirectories();

        $directories[] = __DIR__.'/../../../app/models';

        $this->makeDirectoryStructure($directories);
    }

    /**
     * Copy stubs to test app.
     *
     * @param Config $config
     */
    protected function copyStubs(Config $config)
    {
        $this->copyModelStub('usersModel', 'Users');

        $this->copyModelStub('postsModel', 'Posts');

        $this->writeFile(
            $config->getFactoryDirectory('ModelFactory.php'),
            file_get_contents(__DIR__.'/../../_data/Stubs/factory.stub')
        );
    }

    /**
     * Copy a model stub to the test app directory.
     *
     * @param string $stubName
     * @param string $fileName
     */
    protected function copyModelStub($stubName, $fileName)
    {
        $this->writeFile(
            __DIR__."/../../../app/models/{$fileName}.php",
            file_get_contents(__DIR__."/../../_data/Stubs/{$stubName}.stub")
        );
    }

    /**
     * Get an instance of ModelFactory.
     *
     * @return ModelFactory
     */
    public function getModelFactory()
    {
        return new ModelFactory(Factory::create());
    }

    /**
     * Assert that a table is empty.
     *
     * @param string $table
     *
     * @return $this
     */
    public function seeTableIsEmpty($table)
    {
        $connection = $this->getConnection();

        $statement = "SELECT * FROM {$table}";

        $count = $connection->query($statement)->numRows();

        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s].', $table));

        return $this;
    }

    /**
     * Assert that a table exists.
     *
     * @param string $table
     *
     * @return $this
     */
    public function seeTableExists($table)
    {
        $connection = $this->getConnection();

        $this->assertTrue(
            $connection->tableExists($table),
            "Failed asserting that table {$table} exists."
        );

        return $this;
    }

    /**
     * Assert that a table doesn't exist.
     *
     * @param string $table
     *
     * @return $this
     */
    public function seeTableDoesntExist($table)
    {
        $connection = $this->getConnection();

        $this->assertFalse(
            $connection->tableExists($table),
            "Failed asserting that table {$table} does not exist."
        );

        return $this;
    }

    /**
     * Return a database connection.
     *
     * @return Phalcon\Db\Adapter\Pdo
     */
    public function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }

        $dbConfig = $this->getConfig()->get('database');

        $resolver = new ConnectionResolver();

        return $this->connection = $resolver->getConnection($dbConfig);
    }

    /**
     * Drop given tables.
     *
     * @param array $tables
     */
    public function dropTable(array $tables)
    {
        $connection = $this->getConnection();

        foreach ($tables as $table) {
            $connection->dropTable($table);
        }
    }

    /**
     * Create all directories listed in directories array.
     *
     * @param array $directories
     */
    public function makeDirectoryStructure(array $directories)
    {
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory);
            }
        }
    }

    /**
     * Write contents to path.
     *
     * @param string $path
     * @param string $contents
     *
     * @throws WriteError
     */
    public function writeFile($path, $contents)
    {
        try {
            file_put_contents($path, $contents);
        } catch (\Exception $e) {
            throw WriteError::fileWriteFailed($e, $path);
        }
    }
}
