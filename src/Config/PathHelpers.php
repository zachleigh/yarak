<?php

namespace Yarak\Config;

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
     * Return the database directory path.
     *
     * @return string
     */
    public function getDatabaseDirectory()
    {
        return $this->addFinalSlash($this->get(['application', 'databaseDir']));
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
        return $this->addFinalSlash(
            $this->get(['application', 'consoleDir'])
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
        return $this->addFinalSlash(
            $this->get(['application', 'consoleDir'])
        ).'commands/'.$path;
    }

    /**
     * Add a final slash to a path if it doesn't exist.
     *
     * @param string $path
     *
     * @return string
     */
    protected function addFinalSlash($path)
    {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        return $path;
    }
}
