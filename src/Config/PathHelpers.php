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
     * Return the app path.
     *
     * @return string
     */
    public function getAppPath()
    {
        return Str::append($this->get(['application', 'appDir']), '/');
    }

    /**
     * Return the database directory path.
     *
     * @return string
     */
    public function getDatabaseDirectory()
    {
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
        if (!$this->has(['application', 'consoleDir'])) {
            return;
        }

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
        return Str::append(
            $this->get(['application', 'consoleDir']),
            '/'
        ).'commands/'.$path;
    }
}
