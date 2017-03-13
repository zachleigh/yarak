<?php

namespace Yarak\Exceptions;

use Exception;

class FactoryNotFound extends Exception
{
    /**
     * Factory definition can not be found.
     *
     * @param string $command
     *
     * @return static
     */
    public static function factoryDefinitionNotFound($message)
    {
        return new static($message);
    }
}
