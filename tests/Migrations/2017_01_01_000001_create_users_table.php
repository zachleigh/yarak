<?php

use Phalcon\Db\Index;
use Phalcon\Db\Column;
use Phalcon\Db\Adapter\Pdo;
use Yarak\Migrations\Migration;

class CreateUsersTable implements Migration
{
    /**
     * Run the migration.
     *
     * @param Pdo $connection
     */
    public function up(Pdo $connection)
    {
        $connection->createTable(
            'users',
            null,
            [
                'columns' => [
                    new Column('id', [
                        'type'          => Column::TYPE_INTEGER,
                        'size'          => 10,
                        'unsigned'      => true,
                        'notNull'       => true,
                        'autoIncrement' => true,
                    ]),
                    new Column('username', [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 32,
                        'notNull' => true,
                    ]),
                    new Column('password', [
                        'type'    => Column::TYPE_CHAR,
                        'size'    => 40,
                        'notNull' => true,
                    ]),
                    new Column('email', [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 70,
                        'notNull' => true,
                    ]),
                    new Column('created_at', [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                    ])
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
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
        $connection->dropTable('users');
    }
}
