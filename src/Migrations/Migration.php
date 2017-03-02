<?php

namespace Yarak\Migrations;

use Phalcon\Db\Adapter\Pdo;

interface Migration
{
    /**
     * Run the migration.
     *
     * @param Pdo $connection
     */
    public function up(Pdo $connection);

    /**
     * Reverse the migration.
     *
     * @param Pdo $connection
     */
    public function down(Pdo $connection);
}
