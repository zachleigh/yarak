<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;
use Yarak\Console\DirectoryCreator;

class CommandCreatorTest extends \Codeception\Test\Unit
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
            ['namespaces', 'console'],
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

    /**
     * @test
     */
    public function command_creator_does_not_output_nothing_created_message()
    {
        $creator = new DirectoryCreator($logger = new Logger());

        $creator->create(false);

        $logger->clearLog();

        $this->tester->getCommandCreator($logger)->create('DoSomething');

        $this->assertCount(1, $logger->getLog());

        $this->assertFalse(
            $logger->hasMessage(
                '<comment>Nothing created. All directories and files already exist.</comment>'
            ),
            'Falied asserting that nothing created message was not output.'
        );
    }

    /**
     * @test
     */
    public function command_creator_does_not_create_example_command_or_have_example_command_kernel_entry()
    {
        $logger = new Logger();

        $this->tester->getCommandCreator($logger)->create('DoSomething');

        $config = $this->tester->getConfig();

        $this->assertFileNotExists($config->getCommandsDirectory('ExampleCommand.php'));

        $kernel = file_get_contents($config->getConsoleDirectory('Kernel.php'));

        $this->assertNotContains('ExampleCommand::class', $kernel);
    }
}
