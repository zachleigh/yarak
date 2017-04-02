<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class SeederCreatorTest extends \Codeception\Test\Unit
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
    public function seeder_creator_creates_directory_structure_if_not_present()
    {
        $this->tester->removeDatabaseDirectory();

        $this->assertFileNotExists(
            $this->tester->getConfig()->getDatabaseDirectory()
        );

        $path = $this->tester->getSeederCreator()->create('UserTableSeeder');

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function seeder_creator_inserts_correct_class_name()
    {
        $path = $this->tester->getSeederCreator()->create('UserTableSeeder');

        $this->assertContains('UserTableSeeder', file_get_contents($path));
    }

    /**
     * @test
     */
    public function seeder_creator_outputs_success_message()
    {
        $logger = new Logger();

        $path = $this->tester->getSeederCreator($logger)->create('UserTableSeeder');

        $this->assertCount(3, $logger->getLog());

        $this->assertTrue(
            $logger->hasMessage('<info>Created seeder UserTableSeeder.</info>'),
            'Failed asserting that SeederCreator outputs success message when creating seeder.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created database directory.</info>'),
            'Failed asserting that SeederCreator outputs success message when creating database director.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created seeds directory.</info>'),
            'Failed asserting that SeederCreator outputs success message when creating seeds director.'
        );
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\WriteError
     * @expectedExceptionMessage Could not create command UserTableSeeder. Command with name UserTableSeeder already exists.
     */
    public function seeder_creator_doesnt_create_directories_if_they_already_exists()
    {
        $logger = new Logger();

        $this->tester->getSeederCreator($logger)->create('UserTableSeeder');

        $logger->clearLog();

        $this->tester->getSeederCreator($logger)->create('UserTableSeeder');
    }
}
