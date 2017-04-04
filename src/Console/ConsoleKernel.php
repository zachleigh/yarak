<?php

namespace Yarak\Console;

use Yarak\Exceptions\FileNotFound;
use Yarak\Exceptions\InvalidCommand;

class ConsoleKernel
{
    /**
     * User defined commands.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Get all user defined commands.
     *
     * @return array
     */
    public function getCommands()
    {
        if (property_exists($this, 'commands')) {
            return array_map(function ($command) {
                $this->verifyCommand($command);

                return $command;
            }, $this->commands);
        }
    }

    /**
     * Verify user defined commands.
     *
     * @param string $command
     *
     * @throws FileNotFound|InvalidCommand
     */
    protected function verifyCommand($command)
    {
        if (!class_exists($command)) {
            throw FileNotFound::commandNotFound($command);
        }

        $class = new $command();

        if (!is_a($class, Command::class)) {
            throw InvalidCommand::doesntExtendCommand();
        }
    }
}
