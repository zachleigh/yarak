<?php

namespace Yarak\Migrations;

use Phalcon\Db\Adapter\Pdo;

class DatabaseMigrationRepository implements MigrationRepository
{
    /**
     * The active database connection.
     *
     * @var Pdo
     */
    protected $connection = null;

    /**
     * Set the repository connection on the object.
     *
     * @param Pdo $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return true if repository resource exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->connection->tableExists('migrations');
    }

    /**
     * Create a new repository resource.
     */
    public function create()
    {
        $migration = new CreateMigrationsTable();

        $migration->up($this->connection);
    }

    /**
     * Return the migrations that have already been ran.
     *
     * @param array $ran
     * @param int   $steps
     *
     * @return array
     */
    public function getRan($ran = null, $steps = null)
    {
        if ($ran !== null) {
            return $ran;
        }

        if ($steps === null) {
            return $this->connection->fetchAll('SELECT * FROM migrations');
        }

        $lastBatchNumber = $this->getLastBatchNumber($ran);

        $toRollback = $lastBatchNumber - $steps;

        return $this->connection
            ->fetchAll("SELECT * FROM migrations WHERE batch > {$toRollback}");
    }

    /**
     * Get array of all migrations that have been run.
     *
     * @param array $ran
     * @param int   $steps
     *
     * @return array
     */
    public function getRanMigrations($ran = null, $steps = null)
    {
        $ran = $this->getRan($ran, $steps);

        return array_map(function ($item) {
            return $item['migration'];
        }, $ran);
    }

    /**
     * Get array of all migration batch nubmers that have run.
     *
     * @param array $ran
     *
     * @return array
     */
    public function getRanBatchNumbers($ran = null)
    {
        $ran = $this->getRan($ran);

        return array_map(function ($item) {
            return $item['batch'];
        }, $ran);
    }

    /**
     * Get the last number found in the repository.
     *
     * @param array $ran
     *
     * @return int
     */
    public function getLastBatchNumber($ran = null)
    {
        $batchNumbers = $this->getRanBatchNumbers($ran);

        return empty($batchNumbers) ? 0 : max($batchNumbers);
    }

    /**
     * Get the next available batch number.
     *
     * @param array $ran
     *
     * @return int
     */
    public function getNextBatchNumber($ran = null)
    {
        return $this->getLastBatchNumber($ran) + 1;
    }

    /**
     * Insert a new record in the repository.
     *
     * @param string $fileName
     * @param int    $batch
     */
    public function insertRecord($fileName, $batch)
    {
        $this->connection->insert(
            'migrations',
            [$fileName, $batch],
            ['migration', 'batch']
        );
    }

    /**
     * Delete a record from the repository.
     *
     * @param string $fileName
     */
    public function deleteRecord($fileName)
    {
        $this->connection->execute('DELETE FROM migrations WHERE (?)', [$fileName]);
    }
}
