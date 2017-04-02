<?php

namespace Helper;

use Faker\Factory;
use Yarak\Config\Config;
use Yarak\Console\Output\Logger;
use Yarak\DB\ConnectionResolver;
use Yarak\Console\CommandCreator;
use Yarak\DB\Seeders\SeederCreator;
use Yarak\DB\Factories\ModelFactory;
use Yarak\Migrations\FileDate\FileDateMigrator;
use Yarak\DB\DirectoryCreator as DBDirectoryCreator;
use Yarak\Migrations\FileDate\FileDateMigrationCreator;
use Yarak\Console\DirectoryCreator as ConsoleDirectoryCreator;
use Yarak\Migrations\Repositories\DatabaseMigrationRepository;

class Builder extends \Codeception\Module
{
    /**
     * Return a config array.
     *
     * @return array
     */
    public function getConfig()
    {
        return Config::getInstance();
    }

    /**
     * Get the migrator.
     *
     * @param string $type   Type of migrator.
     * @param Logger $logger
     *
     * @return Yarak\Migrations\Migrator
     */
    public function getMigrator($type = 'fileDate', Logger $logger = null)
    {
        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrator(
                Config::getInstance(),
                new ConnectionResolver(),
                new DatabaseMigrationRepository(),
                $this->getLogger($logger)
            );
        }
    }

    /**
     * Get an instance of the migration creator.
     *
     * @param string $type   Type of migration creator.
     * @param Logger $logger
     *
     * @return Yarak\Migrations\MigrationCreator
     */
    public function getMigrationCreator($type = 'fileDate', Logger $logger = null)
    {
        if (ucfirst($type) === 'FileDate') {
            return new FileDateMigrationCreator(
                Config::getInstance(),
                $this->getLogger($logger)
            );
        }
    }

    /**
     * Get an instance of the seeder creator.
     *
     * @param Logger $logger
     *
     * @return SeederCreator
     */
    public function getSeederCreator(Logger $logger = null)
    {
        return new SeederCreator(
            Config::getInstance(),
            $this->getLogger($logger)
        );
    }

    /**
     * Get an instance of the command creator.
     *
     * @param Logger $logger
     *
     * @return CommandCreator
     */
    public function getCommandCreator(Logger $logger = null)
    {
        return new CommandCreator(
            Config::getInstance(),
            $this->getLogger($logger)
        );
    }

    /**
     * Get an instance of DB DirectoryCreator.
     *
     * @param Logger $logger
     *
     * @return DBDirectoryCreator
     */
    public function getDBDirectoryCreator(Logger $logger = null)
    {
        return new DBDirectoryCreator(
            Config::getInstance(),
            $this->getLogger($logger)
        );
    }

    /**
     * Get an instance of console DirectoryCreator.
     *
     * @param Logger $logger
     *
     * @return ConsoleDirectoryCreator
     */
    public function getConsoleDirectoryCreator(Logger $logger = null)
    {
        return new ConsoleDirectoryCreator(
            Config::getInstance(),
            $this->getLogger($logger)
        );
    }

    /**
     * Get an instance of ModelFactory.
     *
     * @return ModelFactory
     */
    public function getModelFactory()
    {
        return new ModelFactory(Factory::create());
    }

    /**
     * Get an instance of Logger.
     *
     * @param  Logger|null $logger
     *
     * @return Logger
     */
    public function getLogger(Logger $logger = null)
    {
        if (!$logger) {
            $logger = new Logger();
        }

        return $logger;
    }
}
