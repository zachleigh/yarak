<?php

namespace Yarak\tests\unit;

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
        
        $databaseDir = $this->tester->getConfig()->getDatabaseDirectory();

        $this->assertFileNotExists($databaseDir);

        $path = $this->tester
            ->getSeederCreator()
            ->create('UserTableSeeder');

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function it_inserts_correct_class_name()
    {
        $path = $this->tester
            ->getMigrationCreator()
            ->create('UserTableSeeder');

        $data = file_get_contents($path);

        $this->assertContains('UserTableSeeder', $data);
    }
}
