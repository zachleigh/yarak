<?php

namespace Helper;

use Yarak\Config\Config;
use Yarak\DB\ConnectionResolver;

class Database extends \Codeception\Module
{
    /**
     * Database connection.
     *
     * @var Phalcon\Db\Adapter\Pdo
     */
    protected $connection;
    
    /**
     * Clear all database tables.
     */
    public function resetDatabase()
    {
        $migrator = $this->getModule('\Helper\Builder')
            ->getMigrator()
            ->setConnection();

        $connection = $migrator->getConnection();

        $connection->dropTable('posts');

        $connection->dropTable('users');

        $connection->dropTable('migrations');

        $this->getModule('\Helper\Filesystem')->removeDatabaseDirectory();
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

        $dbConfig = Config::getInstance()->get('database');

        $resolver = new ConnectionResolver();

        return $this->connection = $resolver->getConnection($dbConfig);
    }
}
