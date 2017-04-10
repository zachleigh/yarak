<?php

namespace Yarak\Config;

use Yarak\Helpers\Str;

trait PathHelpers
{
    /**
     * Get a value from the config array.
     *
     * @param string|array $value
     *
     * @return mixed
     */
    abstract public function get($value);

    /**
     * Return true if config array has given value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    abstract public function has($value);

    /**
     * Validate that a setting exists.
     *
     * @param array $settings
     *
     * @throws InvalidConfig
     */
    abstract public function validate(array $settings);

    /**
     * Return the app path.
     *
     * @return string
     */
    public function getAppPath()
    {
        $this->validate(['application', 'appDir']);

        return Str::append($this->get(['application', 'appDir']), '/');
    }

    /**
     * Return the database directory path.
     *
     * @return string
     */
    public function getDatabaseDirectory()
    {
        $this->validate(['application', 'databaseDir']);

        return Str::append($this->get(['application', 'databaseDir']), '/');
    }

    /**
     * Return the migration directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getMigrationDirectory($path = '')
    {
        return $this->getDatabaseDirectory().'migrations/'.$path;
    }

    /**
     * Return the factory directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getFactoryDirectory($path = '')
    {
        return $this->getDatabaseDirectory().'factories/'.$path;
    }

    /**
     * Return the seeds directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getSeedDirectory($path = '')
    {
        return $this->getDatabaseDirectory().'seeds/'.$path;
    }

    /**
     * Make database directory structure if it doesn't exist.
     */
    public function getAllDatabaseDirectories()
    {
        return [
            'database'   => $this->getDatabaseDirectory(),
            'migrations' => $this->getMigrationDirectory(),
            'factories'  => $this->getFactoryDirectory(),
            'seeds'      => $this->getSeedDirectory(),
        ];
    }

    /**
     * Get the console directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getConsoleDirectory($path = '')
    {
        $this->validate(['application', 'consoleDir']);

        return Str::append(
            $this->get(['application', 'consoleDir']),
            '/'
        ).$path;
    }

    /**
     * Get the commands directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getCommandsDirectory($path = '')
    {
        return $this->getConsoleDirectory().'commands/'.$path;
    }
}
