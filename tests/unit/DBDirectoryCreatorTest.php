<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class DBDirectoryCreatorTest extends \Codeception\Test\Unit
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
    public function db_directory_creator_creates_the_database_directory()
    {
        $this->tester->removeMigrationDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getDatabaseDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created database directory.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating database directory.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_creates_the_migrations_directory()
    {
        $this->tester->removeMigrationDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getMigrationDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created migrations directory.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating migrations directory.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_creates_the_seeds_directory()
    {
        $this->tester->removeSeedDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getSeedDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created seeds directory.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating seeds directory.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_creates_the_factories_directory()
    {
        $this->tester->removeFactoryDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getFactoryDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created factories directory.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating factories directory.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_creates_model_factory_file()
    {
        $fileDir = $this->tester
            ->getConfig()
            ->getFactoryDirectory('ModelFactory.php');

        $this->tester->getFilesystem()->remove($fileDir);

        $logger = $this->assertDirectoryCreatorCreatesPath($fileDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created ModelFactory file.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating ModelFactory.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_creates_database_seeder_file()
    {
        $fileDir = $this->tester
            ->getConfig()
            ->getSeedDirectory('DatabaseSeeder.php');

        $this->tester->getFilesystem()->remove($fileDir);

        $logger = $this->assertDirectoryCreatorCreatesPath($fileDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created DatabaseSeeder file.</info>'),
            'Failed asserting that DirectoryCreator outputs message when creating DatabaseSeeder.'
        );
    }

    /**
     * @test
     */
    public function db_directory_creator_doesnt_create_when_files_already_exist()
    {
        $logger = new Logger();

        $this->tester->getDBDirectoryCreator($logger)->create();

        $logger->clearLog();

        $this->tester->getDBDirectoryCreator($logger)->create();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue($logger->hasMessage(
            '<comment>Nothing created. All directories and files already exist.</comment>',
            'Failed asserting that DirectoryCreator outputs message when nothing created.'
        ));
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\InvalidConfig
     * @expectedExceptionMessage The setting 'application -> databaseDir' can not be found. Please be sure it is set.
     */
    public function console_directory_creator_throws_exception_if_consoleDir_path_not_set()
    {
        $this->tester->getConfig()->remove(['application', 'databaseDir']);

        $this->tester->getDBDirectoryCreator(new Logger())->create();
    }

    /**
     * Assert that the directory creator creates the given path.
     *
     * @param string $path
     */
    protected function assertDirectoryCreatorCreatesPath($path)
    {
        $this->assertFileNotExists($path);

        $logger = new Logger();

        $this->tester->getDBDirectoryCreator($logger)->create();

        $this->assertFileExists($path);

        return $logger;
    }
}
