<?php

namespace Yarak\Commands;

use Yarak\Config\Config;
use Yarak\Output\SymfonyOutput;
use Yarak\DB\ConnectionResolver;
use Symfony\Component\Console\Command\Command;

class YarakCommand extends Command
{
    /**
     * Application config.
     *
     * @var array
     */
    protected $configArray;

    /**
     * Construct.
     *
     * @param array $configArray
     */
    public function __construct(array $configArray)
    {
        parent::__construct();

        $this->configArray = $configArray;
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
        $config = Config::getInstance($this->configArray);

        $migratorClassName = $this->getMigratorClassName($config);

        return new $migratorClassName(
            $config,
            new ConnectionResolver(),
            $this->getRepository($config),
            $symfonyOutput
        );
    }

    /**
     * Get the name of the migrator class.
     *
     * @param Config $config
     *
     * @return string
     */
    protected function getMigratorClassName(Config $config)
    {
        $migratorType = ucfirst($config->get('migratorType'));

        return "Yarak\\Migrations\\$migratorType\\".
            $migratorType.'Migrator';
    }

    /**
     * Get an instance of MigrationRepository.
     *
     * @param Config $config
     *
     * @return Yarak\Migrations\MigrationRepository
     */
    protected function getRepository(Config $config)
    {
        $repositoryType = ucfirst($config->get('migrationRepository'));

        $repositoryClass = 'Yarak\\Migrations\\Repositories\\'.
            $repositoryType.'MigrationRepository';

        return new $repositoryClass();
    }
}
