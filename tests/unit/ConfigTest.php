<?php

namespace Yarak\tests\unit;

class ConfigTest extends \Codeception\Test\Unit
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
    public function it_gets_default_values()
    {
        $config = $this->tester->getConfig();

        $default = $config->getDefault('migrationRepository');

        $this->assertEquals('database', $default);
    }

    /**
     * @test
     */
    public function it_gets_default_values_through_get()
    {
        $config = $this->tester->getConfig();

        $default = $config->get('migrationRepository');

        $this->assertEquals('database', $default);
    }
}
