<?php

namespace Yarak\Migrations;

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
