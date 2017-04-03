<?php

namespace Yarak\tests\unit;

use Phalcon\DI;
use Yarak\Yarak;
use Yarak\Kernel;
use Yarak\Console\Output\Logger;
use Yarak\Console\DirectoryCreator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class UserCommandTest extends \Codeception\Test\Unit
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
    public function user_defined_commands_work()
    {
        $creator = new DirectoryCreator(new Logger());

        $creator->create();

        $output = Yarak::call('example', [
            'word'        => 'example',
            '--backwards' => true
        ], DI::getDefault(), true)->fetch();

        $this->assertEquals(
            "Example spelled backwards is e-l-p-m-a-x-e.\n",
            $output
        );
    }
}
