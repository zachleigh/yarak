<?php

namespace Yarak\Exceptions;

use Exception;

class FileNotFound extends Exception
{
    /**
     * The services file can not be resolved.
     *
     * @param string $additional
     *
     * @return static
     */
    public static function servicesFileNotFound($additional = '')
    {
        return new static(
            'The services file could not be found at app/config/services.php. '.
            $additional
        );
    }

    /**
     * The migration file can not be found.
     *
     * @param string $migrationFileName
     *
     * @return static
     */
    public static function migrationFileNotFound($migrationFileName, $path)
    {
        return new static(
            "The migration file {$migrationFileName} could not be found at {$path}"
        );
    }
}
