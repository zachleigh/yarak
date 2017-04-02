<?php

namespace Yarak\Helpers;

use Yarak\Console\Output\Output;
use Yarak\Exceptions\WriteError;

trait Filesystem
{
    /**
     * Create all directories listed in directories array.
     *
     * @param array  $directories
     * @param Output $output
     */
    protected function makeDirectoryStructure(array $directories, Output $output = null)
    {
        foreach ($directories as $key => $directory) {
            if (!file_exists($directory)) {
                mkdir($directory);

                if ($output) {
                    $output->writeInfo("Created {$key} directory.");
                }
            }
        }
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
