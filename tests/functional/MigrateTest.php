<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MigrateTest extends \Codeception\Test\Unit
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
    public function it_runs_migrations()
    {
        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->seeTableDoesntExist('migrations');

        $this->tester->createMigration();

        $this->tester->createMigration('2017_01_01_000002_create_posts_table.php');

        Yarak::call('migrate', [], DI::getDefault());

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
            'batch'     => 1,
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
            'batch'     => 1,
        ]);
    }
}
