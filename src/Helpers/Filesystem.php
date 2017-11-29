<?php

namespace Yarak\Helpers;

use Yarak\Console\Output\Output;
use Yarak\Exceptions\WriteError;

trait Filesystem
{
    /**
     * Create all directories listed in directories array.
     * Return the number of created directories
     *
     * @param array  $directories
     * @param Output $output
     * @return  int
     */
    protected function makeDirectoryStructure(array $directories, Output $output = null)
    {
        $createdDirs = 0;

        foreach ($directories as $key => $directory) {
            if (!file_exists($directory)) {
                if (mkdir($directory)) {
                    $createdDirs++;
                }

                if ($output) {
                    $output->writeInfo("Created {$key} directory.");
                }
            }
        }

        return $createdDirs;
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
