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
}
