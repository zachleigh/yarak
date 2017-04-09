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
        Config::getInstance()->set(['namespaces', 'root'], 'Test');

        $namespace = NamespaceResolver::resolve('console');

        $this->assertEquals('Test\\Console', $namespace);
    }

    /**
     * @test
     */
    public function namespace_resolver_uses_file_path_if_not_root_namespace()
    {
        Config::getInstance()->remove(['namespaces', 'root']);

        $namespace = NamespaceResolver::resolve('console');

        $this->assertEquals('App\\Console', $namespace);
    }

    /**
     * @test
     */
    public function namespace_resolver_creates_namespace_from_path()
    {
        $namespace = NamespaceResolver::resolve('my/custom/path');

        $this->assertEquals('MyApp\\My\\Custom\\Path', $namespace);
    }
}
