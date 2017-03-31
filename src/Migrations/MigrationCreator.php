<?php

namespace Yarak\Migrations;

use Yarak\Config\Config;
use Yarak\Console\Output\Output;

interface MigrationCreator
{
    /**
     * Create a migration file.
     *
     * @param string $name
     * @param string $create
     *
     * @return string
     */
    public function create($name, $create = false);
}
