<?php

namespace Yarak\Migrations;

use Yarak\Config\Config;

interface MigrationCreator
{
    /**
     * Construct.
     *
     * @param Config $config
     */
    public function __construct(Config $config);

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
