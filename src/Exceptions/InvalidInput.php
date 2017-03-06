<?php

namespace Yarak\Exceptions;

use Exception;

class InvalidInput extends Exception
{
    /**
     * The given command is invalid.
     *
     * @param string $command
     *
     * @return static
     */
    public static function invalidCommand($command)
    {
        return new static("The command {$command} does not exist.");
    }
}
