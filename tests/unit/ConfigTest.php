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
    public function config_gits_values_through_property_call()
    {
        $this->assertEquals(
            'MyApp',
            $this->tester->getConfig()->namespaces->root
        );
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
    public function config_gets_default_values_through_property_call()
    {
        $this->assertEquals(
            'database',
            $this->tester->getConfig()->migrationRepository
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
        $this->assertFalse(
            $this->tester->getConfig()->has(['not', 'real', 'path'])
        );

        $this->tester->getConfig()->set(['not', 'real', 'path'], 'value');

        $this->assertEquals(
            'value',
            $this->tester->getConfig()->not->real->path
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
            $this->tester->getConfig()->not->real->path
        );

        $this->tester->getConfig()->set(['not', 'real', 'path'], 'updated');

        $this->assertEquals(
            'updated',
            $this->tester->getConfig()->not->real->path
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
            $this->tester->getConfig()->not->real->path
        );

        $this->tester->getConfig()->remove(['not', 'real', 'path']);

        $this->assertNull(
            $this->tester->getConfig()->not->real->path
        );
    }

    /**
     * @test
     */
    public function config_refreshes_the_config_array()
    {
        $config = $this->tester->getConfig();

        $config->set(['namespaces', 'root'], 'Wrong');

        $this->assertEquals('Wrong', $config->namespaces->root);

        $config->refresh();

        $this->assertEquals('MyApp', $config->namespaces->root);
    }

    /**
     * @test
     */
    public function user_can_set_single_dimension_config_path()
    {
        $config = $this->tester->getConfig();

        $this->assertEquals(
            ['database', 'application', 'namespaces'],
            array_keys($config->toArray())
        );

        $config->setConfig('namespaces');

        $this->assertEquals(
            ['root' => 'MyApp'],
            $config->toArray()
        );
    }

    /**
     * @test
     */
    public function user_can_set_multi_dimension_config_path()
    {
        $config = $this->tester->getConfig();

        $this->assertEquals(
            ['database', 'application', 'namespaces'],
            array_keys($config->toArray())
        );

        $config->setConfig('database.password');

        $this->assertEquals(
            'password',
            $config->toArray()
        );
    }

    /**
     * @test
     */
    public function user_can_set_multiple_fields_using_set_config()
    {
        $config = $this->tester->getConfig();

        $this->assertFalse($config->has(['namespaces', 'myNamespace']));

        $this->assertFalse($config->has(['application', 'myDir']));

        $config->setConfig([
            'namespaces' => [
                'myNamespace' => 'App\\MyNamespace'
            ],
            'application' => [
                'myDir' => '/path/to/myDir'
            ]
        ]);

        $this->assertEquals(
            'App\\MyNamespace',
            $config->namespaces->myNamespace
        );

        $this->assertEquals(
            '/path/to/myDir',
            $config->application->myDir
        );
    }

    /**
     * @test
     */
    public function user_can_set_own_config()
    {
        $config = $this->tester->getConfig();

        $this->assertFalse($config->has(['namespaces', 'myNamespace']));

        $this->assertFalse($config->has(['application', 'myDir']));

        $config->setConfig([
            'namespaces' => [
                'myNamespace' => 'App\\MyNamespace'
            ],
            'application' => [
                'myDir' => '/path/to/myDir'
            ]
        ], false);

        $this->assertCount(2, $config->toArray());

        $this->assertEquals(
            'App\\MyNamespace',
            $config->namespaces->myNamespace
        );

        $this->assertEquals(
            '/path/to/myDir',
            $config->application->myDir
        );
    }
}
