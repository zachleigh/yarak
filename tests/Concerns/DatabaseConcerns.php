<?php

namespace Yarak\Tests\Concerns;

trait DatabaseConcerns
{
    /**
     * Return a database connection.
     *
     * @return Phalcon\Db\Adapter\Pdo
     */
    abstract public function getConnection();

    /**
     * Assert that a given where condition exists in the database.
     *
     * @param string $table
     * @param array  $data
     *
     * @return $this
     */
    protected function seeInDatabase($table, array $data)
    {
        $connection = $this->getConnection();

        $values = array_values($data);

        $statement = $this->prepareStatement($table, $data);

        $count = $connection->query($statement, $values)->numRows();

        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));

        return $this;
    }

    /**
     * Assert that a given where condition does not exist in the database.
     *
     * @param string $table
     * @param array  $data
     *
     * @return $this
     */
    protected function dontSeeInDatabase($table, array $data)
    {
        $connection = $this->getConnection();

        $values = array_values($data);

        $statement = $this->prepareStatement($table, $data);

        $count = $connection->query($statement, $values)->numRows();

        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));

        return $this;
    }

    /**
     * Prepare a sql statement.
     *
     * @param string $table
     * @param array  $data
     *
     * @return string
     */
    protected function prepareStatement($table, array $data)
    {
        $keys = array_keys($data);

        $statement = "SELECT * FROM {$table} WHERE ";

        $statement .= implode('=? and ', $keys).'=?';

        return $statement;
    }

    /**
     * Assert that a table is empty.
     *
     * @param string $table
     *
     * @return $this
     */
    protected function seeTableIsEmpty($table)
    {
        $connection = $this->getConnection();

        $statement = "SELECT * FROM {$table}";

        $count = $connection->query($statement)->numRows();

        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s].', $table));

        return $this;
    }

    /**
     * Assert that a table exists.
     *
     * @param string $table
     *
     * @return $this
     */
    protected function seeTableExists($table)
    {
        $connection = $this->getConnection();

        $this->assertTrue(
            $connection->tableExists($table),
            "Failed asserting that table {$table} exists."
        );

        return $this;
    }

    /**
     * Assert that a table doesn't exist.
     *
     * @param string $table
     *
     * @return $this
     */
    protected function seeTableDoesntExist($table)
    {
        $connection = $this->getConnection();

        $this->assertFalse(
            $connection->tableExists($table),
            "Failed asserting that table {$table} does not exist."
        );

        return $this;
    }
}
