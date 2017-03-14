<?php

namespace Yarak\tests\functional;

use Yarak\Yarak;
use Yarak\tests\TestCase;
use Yarak\tests\Concerns\DatabaseConcerns;

class MigrateTest extends TestCase
{
    use DatabaseConcerns;

    /**
     * @test
     */
    public function it_runs_migrations()
    {
        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $this->seeTableDoesntExist('migrations');

        $this->createMigration();

        $this->createMigration('2017_01_01_000002_create_posts_table.php');

        Yarak::call('migrate', [], $this->getConfig()->getAll());

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 1,
        ]);
    }

    /**
     * @test
     */
    public function it_rollsback_single_step()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true,
        ], $this->getConfig()->getAll());

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_rollsback_mutliple_steps()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true,
            '--steps'    => 2,
        ], $this->getConfig()->getAll());

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_rollsback_last_step()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--rollback' => true,
        ], $this->getConfig()->getAll());

        $this->seeTableExists('users');

        $this->seeTableDoesntExist('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_resets_the_database()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate', [
            '--reset' => true,
        ], $this->getConfig()->getAll());

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }

    /**
     * @test
     */
    public function it_refreshes_the_database()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 2,
        ]);

        Yarak::call('migrate', [
            '--refresh' => true,
        ], $this->getConfig()->getAll());

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 1,
        ]);
    }
}
