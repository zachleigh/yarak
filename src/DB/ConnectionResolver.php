<?php

namespace Yarak\DB;

use Phalcon\Exception;

class ConnectionResolver
{
    /**
     * Get connection to database.
     *
     * @param array $dbConfig
     *
     * @throws Exception
     *
     * @return \Phalcon\Db\Adapter\Pdo
     */
    public function getConnection(array $dbConfig)
    {
        $dbClass = sprintf('\Phalcon\Db\Adapter\Pdo\%s', $dbConfig['adapter']);

        if (!class_exists($dbClass)) {
            throw new Exception(
                sprintf('PDO adapter "%s" not found.', $dbClass)
            );
        }

        unset($dbConfig['adapter']);

        return new $dbClass($dbConfig);
    }
}
