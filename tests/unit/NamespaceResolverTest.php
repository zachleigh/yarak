<?php

namespace Yarak\tests\unit;

use Yarak\Config\Config;
use Yarak\Helpers\NamespaceResolver;

class NamespaceResolverTest extends \Codeception\Test\Unit
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
    public function namespace_resolver_uses_set_root_namespace()
    {
        $config = Config::getInstance();

        $config->set(['namespaces', 'root'], 'Test');

        $namespace = NamespaceResolver::resolve('console');

        $config->set(['namespaces', 'root'], 'App');

        $this->assertEquals('Test\\Console', $namespace);
    }

    /**
     * @test
     */
    public function namespace_resolver_uses_file_path_if_not_root_namespace()
    {
        $config = Config::getInstance();

        $config->remove(['namespaces', 'root']);

        $namespace = NamespaceResolver::resolve('console');

        $config->set(['namespaces', 'root'], 'App');

        $this->assertEquals('App\\Console', $namespace);
    }
}
