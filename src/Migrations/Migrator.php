<?php

namespace Yarak\Migrations;

use Yarak\Config\Config;
use Yarak\DB\ConnectionResolver;
use Yarak\Migrations\Repositories\MigrationRepository;

interface Migrator
{
    /**
     * Construct.
     *
     * @param Config              $config
     * @param ConnectionResolver  $resolver
     * @param MigrationRepository $repository
     */
    public function __construct(
        Config $config,
        ConnectionResolver $resolver,
        MigrationRepository $repository
    );

    /**
     * Run migrations.
     *
     * @return array
     */
    public function run();

    /**
     * Rollback migrations.
     *
     * @param int $steps
     *
     * @return array
     */
    public function rollback($steps = 1);

    /**
     * Reset the database by rolling back all migrations.
     *
     * @return array
     */
    public function reset();

    /**
     * Reset the database and run all migrations.
     *
     * @return array
     */
    public function refresh();

    /**
     * Set connection to database on object.
     *
     * @return Pdo
     */
    public function setConnection();

    /**
     * Return the connection.
     *
     * @return \Phalcon\Db\Adapter\Pdo
     */
    public function getConnection();

    /**
     * Return the object log.
     *
     * @return array
     */
    public function getLog();
}
