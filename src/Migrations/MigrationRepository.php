<?php

namespace Yarak\Migrations;

interface MigrationRepository
{
    /**
     * Set the repository connection on the object.
     *
     * @param Pdo $connection
     */
    public function setConnection($connection);

    /**
     * Return true if repository resource exists.
     *
     * @return bool
     */
    public function exists();

    /**
     * Create a new repository resource.
     */
    public function create();

    /**
     * Return the migrations that have already been ran.
     *
     * @param array $ran
     * @param int   $steps
     *
     * @return array
     */
    public function getRan($ran = null, $steps = null);

    /**
     * Get array of all migrations that have been run.
     *
     * @param array $ran
     * @param int   $steps
     *
     * @return array
     */
    public function getRanMigrations($ran = null, $steps = null);

    /**
     * Get array of all migration batch nubmers that have run.
     *
     * @param array $ran
     *
     * @return array
     */
    public function getRanBatchNumbers($ran = null);

    /**
     * Get the last number found in the repository.
     *
     * @param array $ran
     *
     * @return int
     */
    public function getLastBatchNumber($ran = null);

    /**
     * Get the next available batch number.
     *
     * @param array $ran
     *
     * @return int
     */
    public function getNextBatchNumber($ran = null);

    /**
     * Insert a new record in the repository.
     *
     * @param string $fileName
     * @param int    $batch
     */
    public function insertRecord($fileName, $batch);

    /**
     * Delete a record from the repository.
     *
     * @param string $fileName
     */
    public function deleteRecord($fileName);
}
