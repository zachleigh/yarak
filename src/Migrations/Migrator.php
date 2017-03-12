<?php

namespace Yarak\Migrations;

use Yarak\Helpers\Str;
use Yarak\Config\Config;
use Yarak\Helpers\Loggable;
use Yarak\Helpers\Filesystem;
use Yarak\DB\ConnectionResolver;
use Yarak\Migrations\Repositories\MigrationRepository;

class Migrator
{
    use Filesystem, Loggable;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Database connection resolver.
     *
     * @var ConnectionResolver
     */
    protected $resolver;

    /**
     * Repository for logging migration activity.
     *
     * @var MigrationRepository
     */
    protected $repository;

    /**
     * The active database connection.
     *
     * @var Phalcon\Db\Adapter\Pdo
     */
    protected $connection = null;

    /**
     * Construct.
     *
     * @param Config                       $config
     * @param ConnectionResolver           $resolver
     * @param MigrationRepositoryInterface $repository
     */
    public function __construct(
        Config $config,
        ConnectionResolver $resolver,
        MigrationRepository $repository
    ) {
        $this->config = $config;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }

    /**
     * Run migrations.
     *
     * @return array
     */
    public function run()
    {
        $this->setUp();

        $pendingMigrations = $this->getPendingMigrations();

        return $this->runPending($pendingMigrations);
    }

    /**
     * Get all migration filenames that have not been run.
     *
     * @return array
     */
    protected function getPendingMigrations()
    {
        return array_diff(
            $this->getMigrationFiles(),
            $this->repository->getRanMigrations()
        );
    }

    /**
     * Get array of migration file names from directory listed in config.
     *
     * @return array
     */
    protected function getMigrationFiles()
    {
        $files = scandir($this->config->getMigrationDirectory());

        $files = array_filter($files, function ($file) {
            return strpos($file, '.php') !== false;
        });

        $files = array_map(function ($file) {
            return str_replace('.php', '', $file);
        }, $files);

        return array_values($files);
    }

    /**
     * Run pending migrations.
     *
     * @param array $migrations
     *
     * @return array
     */
    protected function runPending(array $migrations)
    {
        if (count($migrations) === 0) {
            $this->log('<info>No pending migrations to run.</info>');

            return [];
        }

        $batch = $this->repository->getNextBatchNumber();

        $this->connection->begin();

        foreach ($migrations as $migration) {
            $this->runUp($migration, $batch);
        }

        $this->connection->commit();

        return $migrations;
    }

    /**
     * Run the migration.
     *
     * @param string $migration
     * @param int    $batch
     */
    protected function runUp($migration, $batch)
    {
        $migrationClass = $this->resolveMigrationClass($migration);

        try {
            $migrationClass->up($this->connection);
        } catch (\Exception $e) {
            return $this->log("<error>{$e->getMessage()}</error>");
        }

        $this->log("<info>Migrated {$migration}.</info>");

        $this->repository->insertRecord($migration, $batch);
    }

    /**
     * Resolve the migration class from the file name.
     *
     * @param string $migration
     *
     * @return Yarak\Migrations\Migration
     */
    public function resolveMigrationClass($migration)
    {
        require_once $this->config->getMigrationDirectory().$migration.'.php';

        $class = Str::studly(implode('_', array_slice(explode('_', $migration), 4)));

        return new $class();
    }

    /**
     * Rollback migrations.
     *
     * @param int $steps
     *
     * @return array
     */
    public function rollback($steps = 1)
    {
        $this->setUp();

        $toRollback = $this->repository->getRanMigrations(null, $steps);

        return $this->runRollback($toRollback);
    }

    /**
     * Rollback given migrations.
     *
     * @param array $migrations
     *
     * @return array
     */
    protected function runRollback(array $migrations)
    {
        if (count($migrations) === 0) {
            $this->log('<info>Nothing to rollback.</info>');

            return [];
        }

        $this->connection->begin();

        foreach (array_reverse($migrations) as $migration) {
            $this->runDown($migration);
        }

        $this->connection->commit();

        return $migrations;
    }

    /**
     * Rollback the migration.
     *
     * @param string $migration
     */
    protected function runDown($migration)
    {
        $migrationClass = $this->resolveMigrationClass($migration);

        try {
            $migrationClass->down($this->connection);
        } catch (\Exception $e) {
            return $this->log("<error>{$e->getMessage()}</error>");
        }

        $this->log("<info>Rolled back {$migration}.</info>");

        $this->repository->deleteRecord($migration);
    }

    /**
     * Reset the database by rolling back all migrations.
     *
     * @return array
     */
    public function reset()
    {
        $this->setUp();

        $toRollback = $this->repository->getRanMigrations();

        return $this->runRollback($toRollback);
    }

    /**
     * Reset the database and run all migrations.
     *
     * @return array
     */
    public function refresh()
    {
        $this->setUp();

        $toRollback = $this->repository->getRanMigrations();

        $this->runRollback($toRollback);

        $pendingMigrations = $this->getPendingMigrations();

        return $this->runPending($pendingMigrations);
    }

    /**
     * Perform setup procedures for migrations.
     */
    protected function setUp()
    {
        if (!$this->connection) {
            $this->setConnection();
        }

        $this->createMigrationsRepository();

        $this->makeDirectoryStructure($this->config->getAllDatabaseDirectories());
    }

    /**
     * Set connection to database on object.
     *
     * @return Pdo
     */
    public function setConnection()
    {
        $dbConfig = $this->config->get('database');

        $this->connection = $this->resolver->getConnection($dbConfig);

        $this->repository->setConnection($this->connection);

        return $this;
    }

    /**
     * Return the connection.
     *
     * @return Phalcon\Db\Adapter\Pdo
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Create the migrations table if it doesn't exist.
     */
    public function createMigrationsRepository()
    {
        if (!$this->repository->exists()) {
            $this->repository->create();
        }

        return $this;
    }
}
