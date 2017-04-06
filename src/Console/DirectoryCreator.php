<?php

namespace Yarak\Console;

use Yarak\Helpers\Creator;
use Yarak\Helpers\NamespaceResolver;

class DirectoryCreator extends Creator
{
    /**
     * Create all directories and files for console.
     *
     * @param bool $createExample
     * @param bool $verbose
     */
    public function create($createExample = true, $verbose = true)
    {
        $createdDirs = (bool) count($this->makeDirectoryStructure([
            'console'  => $this->config->getConsoleDirectory(),
            'commands' => $this->config->getCommandsDirectory(),
        ], $this->output));

        $createdKernel = $this->createKernel($createExample);

        $createdExample = $createExample ? $this->createExampleCommand() : false;

        if ($verbose) {
            $this->outputNothingCreated([$createdDirs, $createdKernel, $createdExample]);
        }
    }

    /**
     * Create the kernel file.
     *
     * @param bool $createExample
     *
     * @return bool
     */
    protected function createKernel($createExample)
    {
        $path = $this->config->getConsoleDirectory('Kernel.php');

        if (!file_exists($path)) {
            $this->writeFile(
                $path,
                $this->getKernelStub($createExample)
            );

            $this->output->writeInfo('Created kernel file.');

            return true;
        }

        return false;
    }

    /**
     * Get the kernel stub.
     *
     * @param bool $createExample [<description>]
     *
     * @return string
     */
    protected function getKernelStub($createExample)
    {
        $stub = file_get_contents(__DIR__.'/Stubs/kernel.stub');

        $replace = $createExample ? 'ExampleCommand::class' : '//';

        $stub = str_replace('COMMAND', $replace, $stub);

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

    /**
     * Get the example command stub.
     *
     * @return string
     */
    protected function getExampleStub()
    {
        $stub = file_get_contents(__DIR__.'/Stubs/exampleCommand.stub');

        return $this->setNamespace($stub, NamespaceResolver::resolve('console', 'Commands'));
    }
}
