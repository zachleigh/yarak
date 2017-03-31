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
        $default = $this->tester
            ->getConfig()
            ->getDefault('migrationRepository');

        $this->assertEquals('database', $default);
    }

    /**
     * @test
     */
    public function it_gets_default_values_through_get()
    {
        $default = $this->tester->getConfig()->get('migrationRepository');

        $this->assertEquals('database', $default);
    }

    /**
     * @test
     */
    public function has_returns_true_if_item_exists()
    {
        $this->assertTrue(
            $this->tester->getConfig()->has(['application', 'databaseDir'])
        );
    }

    /**
     * @test
     */
    public function has_returns_false_if_item_doesnt_exist()
    {
        $this->assertFalse(
            $this->tester->getConfig()->has(['application', 'notReal'])
        );
    }

    /**
     * @test
     */
    public function it_sets_new_config_items()
    {
        $this->assertNull(
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );

        $this->tester->getConfig()->set(['not', 'real', 'path'], 'value');

        $this->assertEquals(
            'value',
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );
    }

    /**
     * @test
     */
    public function it_updates_config_items()
    {
        $this->tester->getConfig()->set(['not', 'real', 'path'], 'value');

        $this->assertEquals(
            'value',
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );

        $this->tester->getConfig()->set(['not', 'real', 'path'], 'updated');

        $this->assertEquals(
            'updated',
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );
    }

    /**
     * @test
     */
    public function it_removes_config_items()
    {
        $this->tester->getConfig()->set(['not', 'real', 'path'], 'value');

        $this->assertEquals(
            'value',
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );

        $this->tester->getConfig()->remove(['not', 'real', 'path']);

        $this->assertNull(
            $this->tester->getConfig()->get(['not', 'real', 'path'])
        );
    }
}
