<?php

namespace Yarak\Console;

use Yarak\Helpers\Creator;
use Yarak\Helpers\NamespaceResolver;

class DirectoryCreator extends Creator
{
    /**
     * Create all directories and files for console.
     */
    public function create($createExample = true)
    {
        $createdDirs = (bool) count($this->makeDirectoryStructure([
            'console'  => $this->config->getConsoleDirectory(),
            'commands' => $this->config->getCommandsDirectory(),
        ], $this->output));

        $createdKernel = $this->createKernel();

        $createdExample = $createExample ? $this->createExampleCommand() : false;

        $this->outputNothingCreated([$createdDirs, $createdKernel]);
    }

    /**
     * Create the kernel file.
     *
     * @return bool
     */
    protected function createKernel()
    {
        $path = $this->config->getConsoleDirectory('Kernel.php');

        if (!file_exists($path)) {
            $this->writeFile(
                $path,
                $this->getKernelStub()
            );

            $this->output->writeInfo('Created kernel file.');

            return true;
        }

        return false;
    }

    /**
     * Get the kernel stub.
     *
     * @return string
     */
    protected function getKernelStub()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/kernel.stub');

        return $this->setNamespace($stub, NamespaceResolver::resolve('console'));
    }

    /**
     * Create an example command.
     *
     * @return bool
     */
    protected function createExampleCommand()
    {
        $path = $this->config->getCommandsDirectory('ExampleCommand.php');

        if (!file_exists($path)) {
            $this->writeFile(
                $path,
                $this->getExampleStub()
            );

            $this->output->writeInfo('Created example command file.');

            return true;
        }

        return false;
    }

    protected function getExampleStub()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/exampleCommand.stub');

        return $this->setNamespace($stub, NamespaceResolver::resolve('console', 'Commands'));
    }
}
