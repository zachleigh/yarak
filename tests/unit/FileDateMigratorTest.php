<?php

namespace Yarak\tests\unit;

use Yarak\Yarak;
use Yarak\tests\TestCase;
use Yarak\tests\Concerns\DatabaseConcerns;

class FileDateMigratorTest extends TestCase
{
    use DatabaseConcerns;

    public function setUp()
    {
        parent::setUp();

        Yarak::call('migrate', ['--reset' => true], $this->getConfig()->toArray());

        $this->removeMigrationDirectory();
    }

    /**
     * @test
     */
    public function it_creates_migration_table_if_it_doesnt_exist_when_running()
    {
        $migrator = $this->getMigrator()->setConnection();

        $connection = $migrator->getConnection();

        $connection->dropTable('migrations');

        $this->assertFalse($connection->tableExists('migrations'));

        $migrator->run();

        $this->assertTrue($connection->tableExists('migrations'));
    }

    /**
     * @test
     */
    public function it_logs_message_if_no_pending_migrations()
    {
        $migrator = $this->getMigrator();

        $migrator->run();

        $log = $migrator->getLog();

        $this->assertCount(1, $log);

        $this->assertContains('<info>No pending migrations to run.</info>', $log);
    }

    /**
     * @test
     */
    public function it_runs_a_single_migration()
    {
        $path = $this->createMigration();

        $migrator = $this->getMigrator();

        $migrator->run();

        $this->seeTableExists('users');
    }

    /**
     * @test
     */
    public function it_inserts_migration_record_when_migration_is_run()
    {
        $path = $this->createMigration();

        $fileName = $this->getFileNameFromPath($path);

        $this->dontSeeInDatabase('migrations', [
            'migration' => $fileName,
        ]);

        $migrator = $this->getMigrator();

        $migrator->run();

        $this->seeInDatabase('migrations', [
            'migration' => $fileName,
        ]);
    }

    /**
     * @test
     */
    public function it_sets_batch_number_when_running_single_migration()
    {
        $path = $this->createMigration();

        $fileName = $this->getFileNameFromPath($path);

        $migrator = $this->getMigrator();

        $migrator->run();

        $this->seeInDatabase('migrations', [
            'migration' => $fileName,
            'batch'     => 1,
        ]);
    }

    /**
     * @test
     */
    public function it_logs_run_migration_events()
    {
        $path = $this->createMigration();

        $migrator = $this->getMigrator();

        $migrator->run();

        $log = $migrator->getLog();

        $this->assertCount(1, $log);

        $this->assertContains(
            '<info>Migrated 2017_01_01_000001_create_users_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_only_runs_migration_if_it_hasnt_been_run_yet()
    {
        $path = $this->createMigration();

        $migrator = $this->getMigrator();

        $migrator->run();

        $log = $migrator->getLog();

        $this->assertCount(1, $log);

        $this->assertContains(
            '<info>Migrated 2017_01_01_000001_create_users_table.</info>',
            $log
        );

        $migrator->run();

        $log = $migrator->getLog();

        $this->assertCount(2, $log);

        $this->assertContains(
            '<info>No pending migrations to run.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_runs_multiple_migrations()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $log = $migrator->getLog();

        $this->assertCount(2, $log);

        $this->assertContains(
            '<info>Migrated 2017_01_01_000001_create_users_table.</info>',
            $log
        );

        $this->assertContains(
            '<info>Migrated 2017_01_01_000002_create_posts_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_inserts_same_batch_number_when_two_migrations_are_run_at_once()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

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
    public function it_increments_batch_number_when_migrations_are_run_at_different_times()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 2,
        ]);
    }

    /**
     * @test
     */
    public function it_inserts_migration_repository_records_in_order()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $log = $migrator->getLog();

        $this->assertEquals(
            '<info>Migrated 2017_01_01_000001_create_users_table.</info>',
            $log[0]
        );

        $this->assertEquals(
            '<info>Migrated 2017_01_01_000002_create_posts_table.</info>',
            $log[1]
        );
    }

    /**
     * @test
     */
    public function it_logs_message_if_nothing_to_rollback()
    {
        $migrator = $this->getMigrator();

        $migrator->rollback();

        $log = $migrator->getLog();

        $this->assertCount(1, $log);

        $this->assertContains('<info>Nothing to rollback.</info>', $log);
    }

    /**
     * @test
     */
    public function it_rollsback_a_single_migration()
    {
        $this->createMigration();

        $migrator = $this->getMigrator();

        $migrator->run();

        $this->seeTableExists('users');

        $migrator->rollback();

        $this->seeTableDoesntExist('users');

        $log = $migrator->getLog();

        $this->assertCount(2, $log);

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000001_create_users_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_rollsback_multiple_migrations_in_single_step()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $migrator->rollback(1);

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $log = $migrator->getLog();

        $this->assertCount(4, $log);

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000002_create_posts_table.</info>',
            $log
        );

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000001_create_users_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_removes_filename_record_when_migration_is_rolledback()
    {
        $path = $this->createMigration();

        $fileName = $this->getFileNameFromPath($path);

        $migrator = $this->getMigrator();

        $migrator->run();

        $this->seeInDatabase('migrations', [
            'migration' => $fileName,
        ]);

        $migrator->rollback();

        $this->dontSeeInDatabase('migrations', [
            'migration' => $fileName,
        ]);
    }

    /**
     * @test
     */
    public function it_rolls_back_a_single_step_by_default()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeTableExists('posts');

        $migrator->rollback();

        $this->seeTableExists('users');

        $this->seeTableDoesntExist('posts');

        $log = $migrator->getLog();

        $this->assertCount(3, $log);

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000002_create_posts_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_rolls_back_multiple_steps()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $migrator->rollback(2);

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $log = $migrator->getLog();

        $this->assertCount(4, $log);

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000002_create_posts_table.</info>',
            $log
        );

        $this->assertContains(
            '<info>Rolled back 2017_01_01_000001_create_users_table.</info>',
            $log
        );
    }

    /**
     * @test
     */
    public function it_rolls_back_migrations_in_reverse_order()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $migrator->rollback();

        $log = $migrator->getLog();

        $this->assertEquals(
            '<info>Rolled back 2017_01_01_000002_create_posts_table.</info>',
            $log[2]
        );

        $this->assertEquals(
            '<info>Rolled back 2017_01_01_000001_create_users_table.</info>',
            $log[3]
        );
    }

    /**
     * @test
     */
    public function it_only_removes_rolled_back_migrations_from_migrations_table()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $this->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        $migrator->rollback();

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

        $migrator->reset();

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $log = $migrator->getLog();

        $this->assertCount(4, $log);
    }

    /**
     * @test
     */
    public function it_refreshes_the_database_when_nothing_to_rollback()
    {
        $migrator = $this->getMigrator();

        $this->createSingleStep($migrator);

        $migrator->rollback();

        $this->seeTableDoesntExist('users');

        $this->seeTableDoesntExist('posts');

        $migrator->refresh();

        $this->seeTableExists('users');

        $this->seeTableExists('posts');
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

        $migrator->refresh();

        $this->seeTableExists('users');

        $this->seeTableExists('posts');

        $log = $migrator->getLog();

        $this->assertCount(6, $log);
    }

    /**
     * @test
     */
    public function it_refreshes_the_database_in_single_step()
    {
        $migrator = $this->getMigrator();

        $this->createTwoSteps($migrator);

        $migrator->refresh();

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
