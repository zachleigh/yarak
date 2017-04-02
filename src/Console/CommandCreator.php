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
     * @throws WriteError
     *
     * @return string
     */
    public function create($name)
    {
        $path = $this->config->getCommandsDirectory($name.'.php');

        if (!file_exists($path)) {
            $creator = new DirectoryCreator($this->output);

            $creator->create();

            $this->writeFile($path, $this->getStub($name));

            $this->output->writeInfo("Created command {$name}.");

            return $path;
        }

        throw WriteError::commandExists($name);
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
