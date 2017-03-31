<?php

namespace Yarak\Console;

use Yarak\Helpers\Creator;
use Yarak\Exceptions\WriteError;

class CommandCreator extends Creator
{
    /**
     * Create a new command with given name.
     *
     * @param string $name
     *
     * @return string
     */
    public function create($name)
    {
        if (class_exists($name)) {
            throw WriteError::classExists($name);
        }

        $directoryCreator = new DirectoryCreator($this->config, $this->output);

        $directoryCreator->create();

        $commandsDir = $this->config->getCommandsDirectory();

        $this->writeFile(
            $path = $commandsDir.$name.'.php',
            $this->getStub($name)
        );

        $this->output->writeInfo("Successfully created command {$name}.");

        return $path;
    }

    /**
     * Get the stub file and insert name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        $stub = file_get_contents(__DIR__.'/Stubs/command.stub');

        $stub = str_replace('CLASSNAME', $name, $stub);

        return $this->setNamespace($stub, $this->resolveCommandNamespace());
    }

    /**
     * Resolve the command namespace.
     *
     * @return string
     */
    protected function resolveCommandNamespace()
    {
        if ($this->config->has(['namespaces', 'consoleNamespace'])) {
            return $this->config->get(
                ['namespaces', 'consoleNamespace']
            ).'\Commands';
        }

        return $this->guessNamespace($this->config->getCommandsDirectory());
    }
}
