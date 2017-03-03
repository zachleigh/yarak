<?php

namespace Yarak\Migrations;

use Phalcon\Db\Column;
use Phalcon\Db\Adapter\Pdo;

class CreateMigrationsTable implements Migration
{
    /**
     * Run the migration.
     *
     * @param Pdo $connection
     */
    public function up(Pdo $connection)
    {
        $connection->createTable(
            'migrations',
            null,
            [
                'columns' => [
                    new Column('migration', [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 250,
                        'notNull' => true,
                    ]),
                    new Column('batch', [
                        'type'    => Column::TYPE_INTEGER,
                        'size'    => 10,
                        'notNull' => true,
                    ]),
                ],
            ]
        );
    }

    /**
     * Reverse the migration.
     *
     * @param Pdo $connection
     */
    public function down(Pdo $connection)
    {
        $connection->dropTable('migrations');
    }
}
