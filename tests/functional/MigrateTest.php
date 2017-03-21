<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MigrateTest extends \Codeception\Test\Unit
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
    public function it_runs_migrations()
    {
        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->seeTableDoesntExist('migrations');

        $this->tester->createMigration();

        $this->tester->createMigration('2017_01_01_000002_create_posts_table.php');

        Yarak::call('migrate', [], DI::getDefault());

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
    public function it_rollsback_single_step()
    {
        $this->tester->createSingleStep();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true
        ], DI::getDefault());

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_rollsback_mutliple_steps()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true,
            '--steps'    => 2,
        ], DI::getDefault());

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_rollsback_last_step()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true
        ], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_resets_the_database()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--reset' => true
        ], DI::getDefault());

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_refreshes_the_database()
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

        Yarak::call('migrate', [
            '--refresh' => true
        ], DI::getDefault());

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
    public function it_seeds_the_database_after_refresh()
    {
        $this->tester->seederSetUp();

        $this->tester->createTwoSteps();

        Yarak::call('migrate', [
            '--refresh' => true,
            '--seed'    => true
        ], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->assertTablesCount(5, 50);
    }

    /**
     * @test
     */
    public function it_seeds_the_database_with_specified_class()
    {
        $this->tester->seederSetUp();

        $this->tester->createTwoSteps();

        Yarak::call('migrate', [
            '--refresh' => true,
            '--seed'    => true,
            '--class'   => 'UsersTableSeeder'
        ], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->assertTablesCount(5, 25);
    }
}
