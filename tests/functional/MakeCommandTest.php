<?php

namespace Yarak\tests\functional;

use Phalcon\DI;
use Yarak\Yarak;

class MakeCommandTest extends \Codeception\Test\Unit
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
    public function it_makes_a_seeder()
    {
        $config = $this->tester->getConfig();

        Yarak::call('make:command', [
            'name' => 'DoSomethingGreat'
        ], DI::getDefault());

        $path = $config->getCommandsDirectory('DoSomethingGreat.php');

        $this->assertFileExists($path);

        $contents = file_get_contents($path);

        $this->assertContains('class DoSomethingGreat', $contents);
    }
}