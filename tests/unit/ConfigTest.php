<?php

namespace Yarak\tests\unit;

use Yarak\tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_default_values()
    {
        $config = $this->getConfig();

        $default = $config->getDefault('migrationRepository');

        $this->assertEquals('database', $default);
    }

    /**
     * @test
     */
    public function it_gets_default_values_through_get()
    {
        $config = $this->getConfig();

        $default = $config->get('migrationRepository');

        $this->assertEquals('database', $default);
    }
}
