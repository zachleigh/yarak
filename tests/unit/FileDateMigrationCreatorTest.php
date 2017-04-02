<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class FileDateMigrationCreatorTest extends \Codeception\Test\Unit
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
    public function file_date_migration_creator_creates_directory_structure_if_not_present()
    {
        $this->tester->removeDatabaseDirectory();

        $this->assertFileNotExists(
            $this->tester->getConfig()->getDatabaseDirectory()
        );

        $path = $this->tester->getMigrationCreator()->create('create_test_table');

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function file_date_migration_creator_makes_an_empty_migration()
    {
        $path = $this->tester->getMigrationCreator()->create('create_test_table');

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function file_date_migration_creator_sets_empty_migration_class_name()
    {
        $path = $this->tester->getMigrationCreator()->create('create_test_table');

        $this->assertContains('class CreateTestTable', file_get_contents($path));
    }

    /**
     * @test
     */
    public function file_date_migration_creator_makes_create_table_migration()
    {
        $path = $this->tester->getMigrationCreator()->create('create_test_table');

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function file_date_migration_creator_sets_create_migration_class_name()
    {
        $path = $this->tester
            ->getMigrationCreator()
            ->create('create_test_table', 'test');

        $this->assertContains('class CreateTestTable', file_get_contents($path));
    }

    /**
     * @test
     */
    public function file_date_migration_creator_sets_create_migration_table_name()
    {
        $path = $this->tester
            ->getMigrationCreator()
            ->create('create_test_table', 'test');

        $data = file_get_contents($path);

        $this->assertContains("'test',", $data);

        $this->assertContains("\$connection->dropTable('test');", $data);
    }

    /**
     * @test
     */
    public function file_date_migration_creator_outputs_success_message()
    {
        $logger = new Logger();

        $path = $this->tester
            ->getMigrationCreator('fileDate', $logger)
            ->create('create_test_table', 'test');

        $this->assertCount(3, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Created migration create_test_table.</info>'),
            'Failed asserting that FileDateMigrationCreator outputs success messaage when creating migration.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created database directory.</info>'),
            'Failed asserting that FileDateMigrationCreator outputs success messaage when creating database directory.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created migrations directory.</info>'),
            'Failed asserting that FileDateMigrationCreator outputs success messaage when creating migrations directory.'
        );
    }
}
