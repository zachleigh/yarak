<?php

namespace Yarak\Console;

use Yarak\Config\Config;
use Yarak\DB\ConnectionResolver;
use Yarak\Console\Output\SymfonyOutput;

class YarakCommand extends Command
{
    /**
     * Application config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Construct.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config = Config::getInstance();
    }

    /**
     * Get an instance of the migrator.
     *
     * @param SymfonyOutput $symfonyOutput
     *
     * @return Migrator
     */
    protected function getMigrator(SymfonyOutput $symfonyOutput)
    {
        $migratorClassName = $this->getMigratorClassName($this->config);

        return new $migratorClassName(
            new ConnectionResolver(),
            $this->getRepository(),
            $symfonyOutput
        );
    }

    /**
     * Get the name of the migrator class.
     *
     * @return string
     */
    protected function getMigratorClassName()
    {
        $migratorType = ucfirst($this->config->get('migratorType'));

        return "Yarak\\Migrations\\$migratorType\\".
            $migratorType.'Migrator';
    }

    /**
     * Get an instance of MigrationRepository.
     *
     * @return Yarak\Migrations\MigrationRepository
     */
    protected function getRepository()
    {
        $repositoryType = ucfirst($this->config->get('migrationRepository'));

        $repositoryClass = 'Yarak\\Migrations\\Repositories\\'.
            $repositoryType.'MigrationRepository';

        return new $repositoryClass();
    }
}
