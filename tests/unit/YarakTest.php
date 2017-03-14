<?php

namespace Yarak\tests\unit;

use Yarak\Yarak;
use Yarak\tests\TestCase;

class YarakTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\InvalidInput
     * @expectedExceptionMessage The command wrong does not exist.
     */
    public function it_throws_exception_when_command_does_not_exist()
    {
        Yarak::call('wrong');
    }
}
