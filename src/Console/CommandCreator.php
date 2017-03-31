<?php

namespace Yarak\Console;

use Yarak\Config\Config;
use Yarak\Helpers\Filesystem;
use Yarak\Console\Output\Output;
use Yarak\Exceptions\WriteError;

class CommandCreator
{
    use Filesystem;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Output strategy.
     *
     * @var Output
     */
    protected $output;

    /**
     * Construct.
     *
     * @param Config $config
     * @param Output $output
     */
    public function __construct(Config $config, Output $output)
    {
        $this->config = $config;
        $this->output = $output;
    }

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

        $commandsDir = $this->config->getCommandsDirectory();

        $this->makeDirectoryStructure([
            $this->config->getConsoleDirectory(), 
            $commandsDir
        ]);

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

        return $this->setNamespace($stub, $this->resolveNamespace());
    }

    /**
     * Resolve the command namespace.
     *
     * @return string
     */
    protected function resolveNamespace()
    {
        if ($this->config->has(['namespaces', 'consoleNamespace'])) {
            return $this->config->get(
                ['namespaces', 'consoleNamespace']
            ).'\Commands';
        }
        
        return $this->guessNamespace($this->config->getCommandsDirectory());
    }

    /**
     * Set the namespace in the command stub.
     *
     * @param string $stub
     * @param string $namespace
     *
     * @return string
     */
    protected function setNamespace($stub, $namespace = null)
    {
        if ($namespace !== null) {
            return str_replace(
                'NAMESPACE',
                "\nnamespace {$namespace};\n",
                $stub
            );
        }

        return str_replace('NAMESPACE', '', $stub);
    }
}
