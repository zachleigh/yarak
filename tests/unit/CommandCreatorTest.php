<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class CommandCreatorTest extends \Codeception\Test\Unit
{
    /**
     * Setup the class.
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->setUp();

        $this->tester->removeConsoleDirectory();
    }

    /**
     * @test
     */
    public function command_creator_creates_directory_structure_if_not_present()
    {
        $this->assertFileNotExists(
            $this->tester->getConfig()->getConsoleDirectory()
        );

        $path = $this->tester->getCommandCreator()->create('DoSomething');

        $this->assertFileExists($path);

        $this->assertFileExists(
            $this->tester->getConfig()->getConsoleDirectory('Kernel.php')
        );
    }

    /**
     * @test
     */
    public function command_creator_inserts_correct_class_name()
    {
        $path = $this->tester->getCommandCreator()->create('DoSomething');

        $this->assertContains('DoSomething', file_get_contents($path));
    }

    /**
     * @test
     */
    public function command_creator_inserts_set_namespace()
    {
        $this->tester->getConfig()->set(
            ['namespaces', 'consoleNamespace'],
            'App\Console'
        );

        $path = $this->tester->getCommandCreator()->create('DoSomething');

        $this->assertContains(
            'namespace App\Console\Commands;',
            file_get_contents($path)
        );

        $this->tester->getConfig()->remove('namespaces');
    }

    /**
     * @test
     */
    public function command_creator_inserts_guessed_namespace()
    {
        $this->tester->getConfig()->remove('namespaces');

        $path = $this->tester->getCommandCreator()->create('DoSomething');

        $this->assertContains(
            'namespace App\Console\Commands;',
            file_get_contents($path)
        );
    }

    /**
     * @test
     */
    public function command_creator_outputs_success_message()
    {
        $logger = new Logger();

        $path = $this->tester->getCommandCreator($logger)->create('DoSomething');

        $this->assertTrue(
            $logger->hasMessage('<info>Created command DoSomething.</info>'),
            'Failed asserting that CommandCreator outputs success message.'
        );
    }

    /**
     * @test
     *
     * @expectedException Yarak\Exceptions\WriteError
     * @expectedExceptionMessage Could not create command DoSomething. Command with name DoSomething already exists. 
     */
    public function command_creator_throws_exception_if_command_already_exists()
    {
        $logger = new Logger();

        $this->tester->getCommandCreator($logger)->create('DoSomething');

        $logger->clearLog();

        $this->tester->getCommandCreator($logger)->create('DoSomething');
    }
}
