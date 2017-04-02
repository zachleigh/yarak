<?php

namespace Yarak\Console;

use Yarak\Helpers\Creator;

class DirectoryCreator extends Creator
{
    /**
     * Create all directories and files for console.
     */
    public function create()
    {
        $createdDirs = (bool) count($this->makeDirectoryStructure([
            'console'  => $this->config->getConsoleDirectory(),
            'commands' => $this->config->getCommandsDirectory(),
        ], $this->output));

        $createdKernel = $this->createKernel();

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

        return $this->setNamespace($stub, $this->resolveConsoleNamespace());
    }

    /**
     * Resolve the console namespace.
     *
     * @return string
     */
    protected function resolveConsoleNamespace()
    {
        if ($this->config->has(['namespaces', 'consoleNamespace'])) {
            return $this->config->get(['namespaces', 'consoleNamespace']);
        }

        return $this->guessNamespace($this->config->getConsoleDirectory());
    }
}
