<?php

namespace Yarak\tests\unit;

use Phalcon\DI;
use Yarak\Yarak;
use Yarak\Console\Output\Logger;

class FileDateMigratorTest extends \Codeception\Test\Unit
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
    public function file_date_migrator_creates_migration_table_if_it_doesnt_exist_when_running()
    {
        $migrator = $this->tester->getMigrator()->setConnection();

        $connection = $migrator->getConnection();

        $connection->dropTable('migrations');

        $this->assertFalse(
            $connection->tableExists('migrations'),
            'Failed asserting that migrations table does not exist.'
        );

        $migrator->run();

        $this->assertTrue(
            $connection->tableExists('migrations'),
            'Failed asserting that migrations table exists.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_outputs_message_if_no_pending_migrations()
    {
        $this->tester->getMigrator('fileDate', $logger = new Logger())->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>No pending migrations to run.</info>'),
            'Failed asserting that FileDateMigrator outputs message when no pending migrations exist.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_runs_a_single_migration()
    {
        $path = $this->tester->createMigration();

        $this->tester->getMigrator()->run();

        $this->tester->seeTableExists('users');
    }

    /**
     * @test
     */
    public function file_date_migrator_inserts_migration_record_when_migration_is_run()
    {
        $path = $this->tester->createMigration();

        $this->tester->seeTableDoesntExist('migrations');

        $this->tester->getMigrator()->run();

        $this->tester->seeInDatabase('migrations', [
            'migration' => $this->tester->getFileNameFromPath($path),
        ]);
    }

    /**
     * @test
     */
    public function file_date_migrator_sets_batch_number_when_running_single_migration()
    {
        $path = $this->tester->createMigration();

        $migrator = $this->tester->getMigrator()->run();

        $this->tester->seeInDatabase('migrations', [
            'migration' => $this->tester->getFileNameFromPath($path),
            'batch'     => 1,
        ]);
    }

    /**
     * @test
     */
    public function file_date_migrator_outputs_run_migration_messages()
    {
        $this->tester->createMigration();

        $logger = new Logger();

        $this->tester->getMigrator('fileDate', $logger)->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that FileDateMigrator outputs message when running migration.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_only_runs_migration_if_it_hasnt_been_run_yet()
    {
        $this->tester->createMigration();

        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $migrator->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that initial migration run outputs success message.'
        );

        $migrator->run();

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>No pending migrations to run.</info>'),
            'Failed asserting that second migration run triggered "no pending migrations" output.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_runs_multiple_migrations()
    {
        $this->tester->createSingleStep(
            $this->tester->getMigrator('fileDate', $logger = new Logger())
        );

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that first migration of two ran.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000002_create_posts_table.</info>'),
            'Failed asserting that second migration of two ran.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_inserts_same_batch_number_when_two_migrations_are_run_at_once()
    {
        $this->tester->createSingleStep();

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
    public function file_date_migrator_increments_batch_number_when_migrations_are_run_at_different_times()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 2,
        ]);
    }

    /**
     * @test
     */
    public function file_date_migrator_inserts_migration_repository_records_in_order()
    {
        $this->tester->createSingleStep(
            $this->tester->getMigrator('fileDate', $logger = new Logger())
        );

        $this->assertEquals(
            '<info>Migrated 2017_01_01_000001_create_users_table.</info>',
            $logger->getLog(0)
        );

        $this->assertEquals(
            '<info>Migrated 2017_01_01_000002_create_posts_table.</info>',
            $logger->getLog(1)
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_logs_message_if_nothing_to_rollback()
    {
        $migrator = $this->tester
            ->getMigrator('fileDate', $logger = new Logger())
            ->rollback();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Nothing to rollback.</info>'),
            'Failed asserting that FileDateMigrator outputs message when nothing to rollback.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_rollsback_a_single_migration()
    {
        $this->tester->createMigration();

        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $migrator->run();

        $this->tester->seeTableExists('users');

        $migrator->rollback();

        $this->tester->seeTableDoesntExist('users');

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that FileDateMigrator outputs message when rolling back.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_rollsback_multiple_migrations_in_single_step()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createSingleStep($migrator);

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $migrator->rollback(1);

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(4, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>'),
            'Failed asserting that FileDateMigrator outputs message when rolling back first of two migrations.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that FileDateMigrator outputs message when rolling back second of two migrations.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_removes_filename_record_when_migration_is_rolledback()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $path = $this->tester->createMigration();

        $fileName = $this->tester->getFileNameFromPath($path);

        $migrator->run();

        $this->tester->seeInDatabase('migrations', [
            'migration' => $fileName,
        ]);

        $migrator->rollback();

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => $fileName,
        ]);
    }

    /**
     * @test
     */
    public function file_date_migrator_rolls_back_a_single_step_by_default()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createTwoSteps($migrator);

        $this->tester->seeTableExists('posts');

        $migrator->rollback();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(3, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>'),
            'Failed asserting that FileDateMigrator rolls back a single step by default.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_rolls_back_multiple_steps()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createTwoSteps($migrator);

        $migrator->rollback(2);

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(4, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>'),
            'Failed asserting that FileDateMigrator rolls back first of two steps.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>'),
            'Failed asserting that FileDateMigrator rolls back second of two steps.'
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_rolls_back_migrations_in_reverse_order()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createSingleStep($migrator);

        $migrator->rollback();

        $this->assertEquals(
            '<info>Rolled back 2017_01_01_000002_create_posts_table.</info>',
            $logger->getLog(2)
        );

        $this->assertEquals(
            '<info>Rolled back 2017_01_01_000001_create_users_table.</info>',
            $logger->getLog(3)
        );
    }

    /**
     * @test
     */
    public function file_date_migrator_only_removes_rolled_back_migrations_from_migrations_table()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createTwoSteps($migrator);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        $migrator->rollback();

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
    public function file_date_migrator_resets_the_database()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createTwoSteps($migrator);

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $migrator->reset();

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(4, $logger->getLog());
    }

    /**
     * @test
     */
    public function file_date_migrator_refreshes_the_database_when_nothing_to_rollback()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createSingleStep($migrator);

        $migrator->rollback();

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $migrator->refresh();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');
    }

    /**
     * @test
     */
    public function file_date_migrator_refreshes_the_database()
    {
        $migrator = $this->tester->getMigrator('fileDate', $logger = new Logger());

        $this->tester->createTwoSteps($migrator);

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $migrator->refresh();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->assertCount(6, $logger->getLog());
    }

    /**
     * @test
     */
    public function file_date_migrator_refreshes_the_database_in_single_step()
    {
        $migrator = $this->tester->getMigrator();

        $this->tester->createTwoSteps($migrator);

        $migrator->refresh();

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 1,
        ]);
    }
}
