<?php

namespace Yarak\Tests\Unit;

use Yarak\Config\Config;
use Yarak\Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_default_values()
    {
        $config = Config::getInstance();

        $default = $config->getDefault('migrationRepository');

        $this->assertEquals('database', $default);
    }

    /**
     * @test
     */
    public function it_gets_default_values_through_get()
    {
        $config = Config::getInstance();

        $default = $config->get('migrationRepository');
// dd($default);
        $this->assertEquals('database', $default);
    }
}
