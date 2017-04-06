<?php

namespace Yarak\tests\unit;

use Phalcon\DI;
use Yarak\Yarak;
use Yarak\Console\Output\Logger;
use Yarak\Console\DirectoryCreator;

class UserCommandTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();

        $creator = new DirectoryCreator(new Logger());

        $creator->create();
    }

    /**
     * @test
     */
    public function user_defined_commands_work()
    {
        $output = Yarak::call('example', [
            'word' => 'example',
            '--backwards' => true,
        ], DI::getDefault(), true)->fetch();

        $this->assertEquals(
            "Example spelled backwards is e-l-p-m-a-x-e.\n",
            $output
        );
    }
}
