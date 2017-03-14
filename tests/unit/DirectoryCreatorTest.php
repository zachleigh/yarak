<?php

namespace Yarak\tests\unit;

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

        $this->assertDirectoryCreatorCreatesPath($migrationsDir);
    }

    /**
     * @test
     */
    public function it_creates_the_seeds_directory()
    {
        $this->tester->removeSeedDirectory();

        $seedsDir = $this->tester->getConfig()->getSeedDirectory();

        $this->assertDirectoryCreatorCreatesPath($seedsDir);
    }

    /**
     * @test
     */
    public function it_creates_the_factories_directory()
    {
        $this->tester->removeFactoryDirectory();

        $factoryDir = $this->tester->getConfig()->getFactoryDirectory();

        $this->assertDirectoryCreatorCreatesPath($factoryDir);
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

        $this->assertDirectoryCreatorCreatesPath($fileDir);
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

        $this->assertDirectoryCreatorCreatesPath($fileDir);
    }

    /**
     * Assert that the directory creator creates the given path.
     *
     * @param string $path
     */
    protected function assertDirectoryCreatorCreatesPath($path)
    {
        $this->assertFileNotExists($path);

        $this->tester->getDirectoryCreator()->create();

        $this->assertFileExists($path);
    }
}
