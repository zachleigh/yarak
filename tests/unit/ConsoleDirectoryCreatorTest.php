<?php

namespace Yarak\tests\unit;

use Yarak\Console\Output\Logger;

class ConsoleDirectoryCreatorTest extends \Codeception\Test\Unit
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
    public function it_creates_the_console_directory()
    {
        $this->tester->removeConsoleDirectory();

        $consoleDir = $this->tester->getConfig()->getConsoleDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath($consoleDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created console directory.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_the_commands_directory()
    {
        $this->tester->removeConsoleDirectory();

        $commandsDir = $this->tester->getConfig()->getCommandsDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath($commandsDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created commands directory.</info>')
        );
    }

    /**
     * @test
     */
    public function it_creates_the_kernel_file()
    {
        $this->tester->removeConsoleDirectory();

        $commandsDir = $this->tester->getConfig()->getConsoleDirectory('Kernel.php');

        $logger = $this->assertDirectoryCreatorCreatesPath($commandsDir);

        $this->assertTrue(
            $logger->hasMessage('<info>Created kernel file.</info>')
        );
    }

    /**
     * @test
     */
    public function it_inserts_set_kernel_namespace()
    {
        $this->tester->getConfig()->set(
            ['namespaces', 'consoleNamespace'],
            'MyApp\Console'
        );

        $this->tester->getConsoleDirectoryCreator()->create();

        $data = file_get_contents(
            $this->tester->getConfig()->getConsoleDirectory('Kernel.php')
        );

        $this->assertContains('namespace MyApp\Console;', $data);

        $this->tester->getConfig()->remove('namespaces');
    }

    /**
     * @test
     */
    public function it_inserts_guessed_kernel_namespace()
    {
        $this->tester->getConfig()->remove('namespaces');

        $this->tester->getConsoleDirectoryCreator()->create();

        $data = file_get_contents(
            $this->tester->getConfig()->getConsoleDirectory('Kernel.php')
        );

        $this->assertContains('namespace App\Console;', $data);
    }

    /**
     * Assert that the directory creator creates the given path.
     *
     * @param string $path
     */
    protected function assertDirectoryCreatorCreatesPath($path)
    {
        $this->assertFileNotExists($path);

        $logger = new Logger();

        $this->tester->getConsoleDirectoryCreator($logger)->create();

        $this->assertFileExists($path);

        return $logger;
    }
}
