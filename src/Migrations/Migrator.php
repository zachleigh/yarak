<?php

namespace Yarak\Migrations;

use Yarak\DB\ConnectionResolver;
use Artisanize\Output\Output;
use Yarak\Migrations\Repositories\MigrationRepository;

interface Migrator
{
    /**
     * Construct.
     *
     * @param ConnectionResolver  $resolver
     * @param MigrationRepository $repository
     * @param Output              $output
     */
    public function __construct(
        ConnectionResolver $resolver,
        MigrationRepository $repository,
        Output $output
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
     * @return $this
     */
    public function setConnection();

    /**
     * Return the connection.
     *
     * @return \Phalcon\Db\Adapter\Pdo
     */
    public function getConnection();
}
