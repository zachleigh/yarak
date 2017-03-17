<?php

namespace Yarak\tests\unit;

use Yarak\DB\Seeders\SeedRunner;

class SeederTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->seederSetUp();
    }

    /**
     * @test
     */
    public function run_method_runs_seeder_run_method()
    {
        $seedRunner = new SeedRunner();

        $this->tester->assertTablesEmpty();

        $seedRunner->run('UsersTableSeeder');

        $this->tester->assertTablesCount(5, 25);
    }

    /**
     * @test
     */
    public function seeder_call_method_calls_run_method_on_given_class()
    {
        $seedRunner = new SeedRunner();

        $this->tester->assertTablesEmpty();

        $seedRunner->run('DatabaseSeeder');

        $this->tester->assertTablesCount(5, 50);
    }

    /**
     * @test
     */
    public function it_logs_seeding_messages()
    {
        $seedRunner = new SeedRunner();

        $seedRunner->run('DatabaseSeeder');

        $log = $seedRunner->getLog();

        $this->assertEquals('<info>Ran seeder class DatabaseSeeder.</info>', $log[0]);

        $this->assertEquals('<info>Ran seeder class UsersTableSeeder.</info>', $log[1]);

        $this->assertEquals('<info>Ran seeder class PostsTableSeeder.</info>', $log[2]);
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FileNotFound
     * @expectedExceptionMessage The seeder file InvalidSeeder could not be found in 
     */
    public function it_throws_exception_for_seeder_file_not_found()
    {
        $seedRunner = new SeedRunner();

        $seedRunner->run('InvalidSeeder');
    }
}
