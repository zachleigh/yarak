<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class DBSeedTest extends \Codeception\Test\Unit
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
    public function it_seeds_the_database_with_simple_seeder()
    {
        $this->tester->assertTablesEmpty();

        Yarak::call('db:seed', [
            'class' => 'UsersTableSeeder'
        ], DI::getDefault());

        $this->tester->assertTablesCount(5, 25);
    }

    /**
     * @test
     */
    public function it_seeds_the_database_using_call_method()
    {
        $this->tester->assertTablesEmpty();

        Yarak::call('db:seed', [], DI::getDefault());

        $this->tester->assertTablesCount(5, 50);
    }
}
