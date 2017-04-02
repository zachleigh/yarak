<?php

namespace Helper;

use App\Models\Posts;
use App\Models\Users;
use Codeception\Actor;

class Assertations extends \Codeception\Module
{
    /**
     * Assert that given user is instance of Users and properties are set.
     *
     * @param Users $user
     */
    public function assertUserInstanceMade(Users $user)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertTrue(is_string($user->username));

        $this->assertTrue(is_string($user->email));

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Assert that given user is instance of user and saved in database.
     *
     * @param Users $user
     * @param Actor $tester
     */
    public function assertUserInstanceCreated(Users $user, Actor $tester)
    {
        $this->assertInstanceOf(Users::class, $user);

        $tester->seeRecord(Users::class, [
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }

    /**
     * Assert that user object has given attributes.
     *
     * @param Users $user
     * @param array $attributes
     */
    public function assertUserHasAttributes(Users $user, array $attributes)
    {
        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals($attributes['username'], $user->username);

        $this->assertEquals($attributes['email'], $user->email);

        $this->assertTrue(is_string($user->password));
    }

    /**
     * Assert that the users and posts tables are empty.
     */
    public function assertTablesEmpty()
    {
        $this->assertCount(0, Users::find());

        $this->assertCount(0, Posts::find());
    }

    /**
     * Assert tables have given number of records.
     *
     * @param int $usersCount
     * @param int $postsCount
     */
    public function assertTablesCount($usersCount, $postsCount)
    {
        $this->assertCount($usersCount, Users::find());

        $this->assertCount($postsCount, Posts::find());
    }

    /**
     * Assert that a table is empty.
     *
     * @param string $table
     *
     * @return $this
     */
    public function seeTableIsEmpty($table)
    {
        $connection = $this->getModule('\Helper\Database')->getConnection();

        $count = $connection->query( "SELECT * FROM {$table}")->numRows();

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
    public function seeTableExists($table)
    {
        $connection = $this->getModule('\Helper\Database')->getConnection();

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
    public function seeTableDoesntExist($table)
    {
        $connection = $this->getModule('\Helper\Database')->getConnection();

        $this->assertFalse(
            $connection->tableExists($table),
            "Failed asserting that table {$table} does not exist."
        );

        return $this;
    }
}
