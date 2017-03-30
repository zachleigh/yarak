<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class DirectoryCreatorTest extends \Codeception\Test\Unit
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
    public function it_creates_the_migrations_directory()
    {
        $this->tester->removeMigrationDirectory();

        $migrationsDir = $this->tester->getConfig()->getMigrationDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath($migrationsDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created migrations directory.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_the_seeds_directory()
    {
        $this->tester->removeSeedDirectory();

        $seedsDir = $this->tester->getConfig()->getSeedDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath($seedsDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created seeds directory.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_the_factories_directory()
    {
        $this->tester->removeFactoryDirectory();

        $factoryDir = $this->tester->getConfig()->getFactoryDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath($factoryDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created factories directory.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_model_factory_file()
    {
        $fileDir = $this->tester
            ->getConfig()
            ->getFactoryDirectory('ModelFactory.php');

        $this->tester->getFilesystem()->remove($fileDir);

        $logger = $this->assertDirectoryCreatorCreatesPath($fileDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created ModelFactory file.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_database_seeder_file()
    {
        $fileDir = $this->tester
            ->getConfig()
            ->getSeedDirectory('DatabaseSeeder.php');

        $this->tester->getFilesystem()->remove($fileDir);

        $logger = $this->assertDirectoryCreatorCreatesPath($fileDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created DatabaseSeeder file.</info>')
        );
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

        $this->tester->getDirectoryCreator($logger)->create();

        $this->assertFileExists($path);

        return $logger;
    }
}
