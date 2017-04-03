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
    public function console_directory_creator_creates_the_console_directory()
    {
        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getConsoleDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created console directory.</info>'),
            'Failed asserting that DirectoryCreator outputs success message when creating console directory.'
        );
    }

    /**
     * @test
     */
    public function console_directory_creator_creates_the_commands_directory()
    {
        $this->tester->removeConsoleDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getCommandsDirectory()
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created commands directory.</info>'),
            'Failed asserting that DirectoryCreator outputs success message when creating command directory.'
        );
    }

    /**
     * @test
     */
    public function console_directory_creator_creates_the_kernel_file()
    {
        $this->tester->removeConsoleDirectory();

        $logger = $this->assertDirectoryCreatorCreatesPath(
            $this->tester->getConfig()->getConsoleDirectory('Kernel.php')
        );

        $this->assertTrue(
            $logger->hasMessage('<info>Created kernel file.</info>'),
            'Failed asserting that DirectoryCreator outputs success message when creating Kernel.'
        );
    }

    /**
     * @test
     */
    public function console_directory_creator_inserts_set_kernel_namespace()
    {
        $this->tester->getConfig()->set(
            ['namespaces', 'console'],
            'MyApp\Console'
        );

        $this->tester->removeConsoleDirectory();

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
    public function console_directory_creator_inserts_guessed_kernel_namespace()
    {
        $this->tester->getConfig()->remove('namespaces');

        $this->tester->removeConsoleDirectory();

        $this->tester->getConsoleDirectoryCreator()->create();

        $data = file_get_contents(
            $this->tester->getConfig()->getConsoleDirectory('Kernel.php')
        );

        $this->assertContains('namespace App\Console;', $data);
    }

    /**
     * @test
     */
    public function console_directory_creator_doesnt_create_directories_if_they_already_exists()
    {
        $logger = new Logger();

        $this->tester->getConsoleDirectoryCreator($logger)->create();

        $logger->clearLog();

        $this->tester->getConsoleDirectoryCreator($logger)->create();

        $this->assertCount(1, $logger->getLog());

        $this->assertTrue($logger->hasMessage(
            '<comment>Nothing created. All directories and files already exist.</comment>',
            'Failed asserting that DirectoryCreator outputs message when nothing created.'
        ));
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
