<?php

namespace Yarak\Tests\Unit;

use Yarak\Tests\TestCase;

class DirectoryCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_the_migrations_directory()
    {
        $migrationsDir = $this->getConfig()->getMigrationDirectory();

        $this->assertDirectoryCreatorCreatesPath($migrationsDir);
    }

    /**
     * @test
     */
    public function it_creates_the_seeds_directory()
    {
        $seedsDir = $this->getConfig()->getSeedDirectory();

        $this->assertDirectoryCreatorCreatesPath($seedsDir);
    }

    /**
     * @test
     */
    public function it_creates_the_factories_directory()
    {
        $factoryDir = $this->getConfig()->getFactoryDirectory();

        $this->assertDirectoryCreatorCreatesPath($factoryDir);
    }

    /**
     * @test
     */
    public function it_creates_model_factory_file()
    {
        $fileDir = $this->getConfig()->getFactoryDirectory('ModelFactory.php');

        $this->assertDirectoryCreatorCreatesPath($fileDir);
    }

    /**
     * @test
     */
    public function it_creates_database_seeder_file()
    {
        $fileDir = $this->getConfig()->getSeedDirectory('DatabaseSeeder.php');

        $this->assertDirectoryCreatorCreatesPath($fileDir);
    }

    /**
     * Assert that the directory creator creates the given path.
     *
     * @param  string $path
     */
    protected function assertDirectoryCreatorCreatesPath($path)
    {
        $this->assertFileNotExists($path);

        $this->getDirectoryCreator()->create();

        $this->assertFileExists($path);
    }
}
