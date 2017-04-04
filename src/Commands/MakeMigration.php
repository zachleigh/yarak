<?php

namespace Yarak\Commands;

use Yarak\Console\YarakCommand;

class MakeMigration extends YarakCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration
                            {name : The name of your migration, words separated by underscores.}
                            {--c|create : The name of the table to create.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file.';

    /**
     * Handle the command.
     */
    protected function handle()
    {
        $creator = $this->getCreator();

        $create = is_null($create = $this->option('create')) ? false : $create;

        $creator->create($this->argument('name'), $create);
    }

    /**
     * Get a the migration creator class.
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    protected function getCreator()
    {
        $migratorType = ucfirst($this->config->get('migratorType'));

        $name = "Yarak\\Migrations\\{$migratorType}\\{$migratorType}MigrationCreator";

        return new $name($this->getOutput());
    }
}
