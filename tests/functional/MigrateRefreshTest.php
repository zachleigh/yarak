<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MigrateRefreshTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();
    }

    /**
     * @test
     */
    public function migrate_refresh_command_refreshes_the_database()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 2,
        ]);

        Yarak::call('migrate:refresh', [], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 1,
        ]);
    }

    /**
     * @test
     */
    public function migrate_refresh_command_seeds_the_database_after_refresh()
    {
        $this->tester->seederSetUp();

        $this->tester->createTwoSteps();

        Yarak::call('migrate:refresh', [
            '--seed'    => true
        ], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->assertTablesCount(5, 50);
    }

    /**
     * @test
     */
    public function migrate_refresh_command_seeds_the_database_with_specified_class()
    {
        $this->tester->seederSetUp();

        $this->tester->createTwoSteps();

        Yarak::call('migrate:refresh', [
            '--seed'    => true,
            '--class'   => 'UsersTableSeeder'
        ], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->assertTablesCount(5, 25);
    }
}
