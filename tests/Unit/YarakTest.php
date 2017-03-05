<?php

namespace Yarak\tests\Unit;

use Yarak\Yarak;
use Yarak\tests\TestCase;

class YarakTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\FileNotFound
     * @expectedExceptionMessage The services file could not be found at app/config/services.php. Try passing the Yarak config array as the third argument to Yarak::call.
     */
    public function it_throws_exception_when_kernel_cant_be_resolved()
    {
        Yarak::call('make:migration', [
            'name'    => 'create_new_table',
        ]);
    }
}
