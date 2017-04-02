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
        $this->createAllDirectories();

        $this->createKernel();
    }

    /**
     * Create cosnole directory structure.
     */
    protected function createAllDirectories()
    {
        $commandsDir = $this->config->getCommandsDirectory();

        $created = $this->makeDirectoryStructure([
            'console'  => $this->config->getConsoleDirectory(),
            'commands' => $commandsDir,
        ]);

        foreach ($created as $key => $value) {
            $this->output->writeInfo("Created {$key} directory.");
        }
    }

    /**
     * Create the kernel file.
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
        }
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
