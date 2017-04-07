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
    public function config_gets_default_values()
    {
        $this->assertEquals(
            'database',
            $this->tester->getConfig()->getDefault('migrationRepository')
        );
    }

    /**
     * @test
     */
    public function config_gets_default_values_through_get()
    {
        $this->assertEquals(
            'database',
            $this->tester->getConfig()->get('migrationRepository')
        );
    }

    /**
     * @test
     */
    public function config_has_method_returns_true_if_item_exists()
    {
        $this->assertTrue(
            $this->tester->getConfig()->has(['application', 'databaseDir']),
            'Failed asserting that Config::has returns true when config item exists.'
        );
    }

    /**
     * @test
     */
    public function config_has_method_returns_false_if_item_doesnt_exist()
    {
        $this->assertFalse(
            $this->tester->getConfig()->has(['application', 'notReal']),
            'Failed asserting that Config::has returns false when config items does not exist.'
        );
    }

    /**
     * @test
     */
    public function config_sets_new_config_items()
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
    public function config_updates_config_items()
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
    public function config_removes_config_items()
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

    /**
     * @test
     */
    public function config_refreshes_the_config_array()
    {
        $config = $this->tester->getConfig();

        $config->set(['namespaces', 'root'], 'Wrong');

        $this->assertEquals('Wrong', $config->get(['namespaces', 'root']));

        $config->refresh();

        $this->assertEquals('MyApp', $config->get(['namespaces', 'root']));
    }
}
