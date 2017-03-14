<?php

namespace Yarak\tests\unit;

use Phalcon\DI;
use Yarak\Yarak;

class YarakTest extends \Codeception\Test\Unit
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
     *
     * @expectedException Yarak\Exceptions\InvalidInput
     * @expectedExceptionMessage The command wrong does not exist.
     */
    public function it_throws_exception_when_command_does_not_exist()
    {
        Yarak::call('wrong', [], DI::getDefault());
    }
}
