<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MakeMigrationTest extends \Codeception\Test\Unit
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
    public function it_makes_basic_migration()
    {
        $config = $this->tester->getConfig();

        Yarak::call('make:migration', [
            'name' => 'create_new_table'
        ], DI::getDefault());

        $files = scandir($config->getMigrationDirectory());

        $this->assertContains('create_new_table', $files[2]);

        $migrationPath = $config->getMigrationDirectory().$files[2];

        $migrationContents = file_get_contents($migrationPath);

        $this->assertContains('class CreateNewTable', $migrationContents);
    }

    /**
     * @test
     */
    public function it_makes_create_migration()
    {      
        $config = $this->tester->getConfig();

        Yarak::call('make:migration', [
            'name'     => 'create_new_table',
            '--create' => 'new',
        ], DI::getDefault());

        $files = scandir($config->getMigrationDirectory());

        $this->assertContains('create_new_table', $files[2]);

        $migrationPath = $config->getMigrationDirectory().$files[2];

        $migrationContents = file_get_contents($migrationPath);

        $this->assertContains('class CreateNewTable', $migrationContents);

        $this->assertContains("'new',", $migrationContents);

        $this->assertContains("\$connection->dropTable('new');", $migrationContents);
    }
}
