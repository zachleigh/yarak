<?php

namespace Yarak\Helpers;

use Yarak\Exceptions\WriteError;

trait Filesystem
{
    /**
     * Create all directories listed in directories array.
     *
     * @param array $directories
     *
     * @return array
     */
    protected function makeDirectoryStructure(array $directories)
    {
        $created = [];

        foreach ($directories as $key => $directory) {
            if (!file_exists($directory)) {
                mkdir($directory);

                $created[$key] = $directory;
            }
        }

        return $created;
    }

    /**
     * Write contents to path.
     *
     * @param string $path
     * @param string $contents
     *
     * @throws WriteError
     */
    protected function writeFile($path, $contents)
    {
        try {
            file_put_contents($path, $contents);
        } catch (\Exception $e) {
            throw WriteError::fileWriteFailed($e, $path);
        }
    }
}
