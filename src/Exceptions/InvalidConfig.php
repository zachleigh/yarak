<?php

namespace Yarak\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    /**
     * A config value can not be found.
     *
     * @param string $value
     *
     * @return static
     */
    public static function configValueNotFound($value)
    {
        return new static("The setting '{$value}' can not be found. Please be sure it is set.");
    }
}
