<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;
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
        $seedRunner = $this->getSeedRunner();

        $this->tester->assertTablesEmpty();

        $seedRunner->run('UsersTableSeeder');

        $this->tester->assertTablesCount(5, 25);
    }

    /**
     * @test
     */
    public function seeder_call_method_calls_run_method_on_given_class()
    {
        $seedRunner = $this->getSeedRunner();

        $this->tester->assertTablesEmpty();

        $seedRunner->run('DatabaseSeeder');

        $this->tester->assertTablesCount(5, 50);
    }

    /**
     * @test
     */
    public function it_logs_seeding_messages()
    {
        $logger = new Logger();

        $seedRunner = new SeedRunner($logger);

        $seedRunner->run('DatabaseSeeder');

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class DatabaseSeeder.</info>')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class UsersTableSeeder.</info>')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class PostsTableSeeder.</info>')
        );
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FileNotFound
     * @expectedExceptionMessage The seeder file InvalidSeeder could not be found in 
     */
    public function it_throws_exception_for_seeder_file_not_found()
    {
        $seedRunner = $this->getSeedRunner();

        $seedRunner->run('InvalidSeeder');
    }

    /**
     * Get an instance of SeedRunner with Logger.
     *
     * @return SeedRunner
     */
    protected function getSeedRunner()
    {
        $logger = new Logger();

        return new SeedRunner($logger);
    }
}
