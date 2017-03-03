<?php

use Phalcon\Db\Index;
use Phalcon\Db\Column;
use Phalcon\Db\Reference;
use Phalcon\Db\Adapter\Pdo;
use Yarak\Migrations\Migration;

class CreatePostsTable implements Migration
{
    /**
     * Run the migration.
     *
     * @param Pdo $connection
     */
    public function up(Pdo $connection)
    {
        $connection->createTable(
            'posts',
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
                    new Column('title', [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 200,
                        'notNull' => true,
                    ]),
                    new Column('body', [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]),
                    new Column('user_id', [
                        'type'     => Column::TYPE_INTEGER,
                        'size'     => 10,
                        'unsigned' => true,
                        'notNull'  => true,
                    ]),
                    new Column('created_at', [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                    ]),
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('user_id', ['user_id']),
                ],
                'references' => [
                    new Reference(
                        'user_idfk',
                        [
                            'referencedTable'   => 'users',
                            'columns'           => ['user_id'],
                            'referencedColumns' => ['id'],
                        ]
                    ),
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
        $connection->dropTable('posts');
    }
}
