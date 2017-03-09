<?php

namespace Yarak\Helpers;

trait Filesystem
{
    /**
     * Create all directories listed in directories array.
     *
     * @param array $directories
     */
    public function makeDirectoryStructure(array $directories)
    {
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory);
            }
        }
    }
}
