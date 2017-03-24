<?php

namespace Yarak\tests\unit;

use Phalcon\DI;
use Yarak\Yarak;
use Yarak\Output\Logger;

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
    public function it_creates_migration_table_if_it_doesnt_exist_when_running()
    {
        $migrator = $this->tester->getMigrator()->setConnection();

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
        $logger = new Logger();

        $this->tester->getMigrator('fileDate', $logger)->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>No pending migrations to run.</info>')
        );
    }

    /**
     * @test
     */
    public function it_runs_a_single_migration()
    {
        $path = $this->tester->createMigration();

        $this->tester->getMigrator()->run();

        $this->tester->seeTableExists('users');
    }

    /**
     * @test
     */
    public function it_inserts_migration_record_when_migration_is_run()
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
    public function it_sets_batch_number_when_running_single_migration()
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
    public function it_logs_run_migration_events()
    {
        $this->tester->createMigration();

        $logger = new Logger();

        $this->tester->getMigrator('fileDate', $logger)->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_only_runs_migration_if_it_hasnt_been_run_yet()
    {
        $this->tester->createMigration();

        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $migrator->run();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>')
        );

        $migrator->run();

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>No pending migrations to run.</info>')
        );
    }

    /**
     * @test
     */
    public function it_runs_multiple_migrations()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $this->tester->createSingleStep($migrator);

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000001_create_users_table.</info>')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Migrated 2017_01_01_000002_create_posts_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_inserts_same_batch_number_when_two_migrations_are_run_at_once()
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
    public function it_increments_batch_number_when_migrations_are_run_at_different_times()
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
    public function it_inserts_migration_repository_records_in_order()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $this->tester->createSingleStep($migrator);

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
    public function it_logs_message_if_nothing_to_rollback()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger)->rollback();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Nothing to rollback.</info>')
        );
    }

    /**
     * @test
     */
    public function it_rollsback_a_single_migration()
    {
        $this->tester->createMigration();

        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $migrator->run();

        $this->tester->seeTableExists('users');

        $migrator->rollback();

        $this->tester->seeTableDoesntExist('users');

        $this->assertCount(2, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_rollsback_multiple_migrations_in_single_step()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $this->tester->createSingleStep($migrator);

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $migrator->rollback(1);

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(4, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_removes_filename_record_when_migration_is_rolledback()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_rolls_back_a_single_step_by_default()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $this->tester->createTwoSteps($migrator);

        $this->tester->seeTableExists('posts');

        $migrator->rollback();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(3, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_rolls_back_multiple_steps()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

        $this->tester->createTwoSteps($migrator);

        $migrator->rollback(2);

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->assertCount(4, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000002_create_posts_table.</info>')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Rolled back 2017_01_01_000001_create_users_table.</info>')
        );
    }

    /**
     * @test
     */
    public function it_rolls_back_migrations_in_reverse_order()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_only_removes_rolled_back_migrations_from_migrations_table()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_resets_the_database()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_refreshes_the_database_when_nothing_to_rollback()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_refreshes_the_database()
    {
        $logger = new Logger();

        $migrator = $this->tester->getMigrator('fileDate', $logger);

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
    public function it_refreshes_the_database_in_single_step()
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
