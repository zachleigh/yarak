<?php

namespace Yarak\Exceptions;

use Exception;

class InvalidCommand extends Exception
{
    /**
     * User defined command does not extend parent command.
     *
     * @return static
     */
    public static function doesntExtendCommand()
    {
        return new static('User defined commands must extend Yarak\Console\Command.');
    }
}
