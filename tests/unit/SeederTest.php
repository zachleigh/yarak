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
    public function seeder_runner_run_method_runs_seeder_run_method()
    {
        $this->tester->assertTablesEmpty();

        $this->getSeedRunner()->run('UsersTableSeeder');

        $this->tester->assertTablesCount(5, 25);
    }

    /**
     * @test
     */
    public function seeder_call_method_calls_run_method_on_given_class()
    {
        $this->tester->assertTablesEmpty();

        $this->getSeedRunner()->run('DatabaseSeeder');

        $this->tester->assertTablesCount(5, 50);
    }

    /**
     * @test
     */
    public function seeder_outputs_seeding_messages()
    {
        $seedRunner = new SeedRunner($logger = new Logger());

        $seedRunner->run('DatabaseSeeder');

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class DatabaseSeeder.</info>'),
            'Failed asserting that seeder outputs primary seeder success message.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class UsersTableSeeder.</info>'),
            'Failed asserting that seeder outputs seeder success message.'
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Running seeder class PostsTableSeeder.</info>'),
            'Failed asserting that seeder outputs seeder success message.'
        );
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FileNotFound
     * @expectedExceptionMessage The seeder file InvalidSeeder could not be found in 
     */
    public function seeder_throws_exception_for_seeder_file_not_found()
    {
        $this->getSeedRunner()->run('InvalidSeeder');
    }

    /**
     * Get an instance of SeedRunner with Logger.
     *
     * @return SeedRunner
     */
    protected function getSeedRunner()
    {
        return new SeedRunner(new Logger());
    }
}
