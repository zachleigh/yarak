<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MigrateResetTest extends \Codeception\Test\Unit
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
    public function migrate_reset_command_resets_the_database()
    {
        $this->tester->createTwoSteps();

        $this->tester->seeTableExists('users');

        $this->tester->seeTableExists('posts');

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->seeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);

        Yarak::call('migrate:reset', [], DI::getDefault());

        $this->tester->seeTableDoesntExist('users');

        $this->tester->seeTableDoesntExist('posts');

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000001_create_users_table',
        ]);

        $this->tester->dontSeeInDatabase('migrations', [
            'migration' => '2017_01_01_000002_create_posts_table',
        ]);
    }
}
