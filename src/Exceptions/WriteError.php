<?php

namespace Yarak\Exceptions;

use Exception;

class WriteError extends Exception
{
    /**
     * Writing to the filesystem failed.
     *
     * @param string $path
     *
     * @return static
     */
    public static function fileWriteFailed($e, $path = '')
    {
        $message = "Writing to {$path} failed."."\n".$e;

        return new static($message);
    }

    /**
     * Writing to the filesystem failed because the given class exists.
     *
     * @param string $class
     *
     * @return static
     */
    public static function classExists($class)
    {
        return new static("Class {$class} already exists.");
    }
}
