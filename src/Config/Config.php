<?php

namespace Yarak\Config;

class Config
{
    /**
     * Instance of self.
     *
     * @var Config
     */
    private static $instance;

    /**
     * Yarak config array.
     *
     * @var array
     */
    protected $configArray;

    /**
     * Private constructor.
     */
    private function __construct(array $configArray)
    {
        $this->configArray = $configArray;
    }

    /**
     * Get instance of self with config array set.
     *
     * @param array $configArray
     *
     * @return Config
     */
    public static function getInstance(array $configArray = [])
    {
        if (empty(self::$instance)) {
            self::$instance = new self($configArray);
        }

        return self::$instance;
    }

    /**
     * Get a value from the config array.
     *
     * @param string|array $value
     *
     * @return mixed
     */
    public function get($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $current = $this->configArray;

        foreach ($value as $configItem) {
            $current = $current[$configItem];
        }

        return $current;
    }

    /**
     * Return config array.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->configArray;
    }

    /**
     * Return the database directory path.
     *
     * @return string
     */
    public function getDatabaseDirectory()
    {
        return $this->get(['application', 'databaseDir']);
    }

    /**
     * Return the migration directory path.
     *
     * @return string
     */
    public function getMigrationDirectory()
    {
        return $this->getDatabaseDirectory().'migrations/';
    }

    /**
     * Return the factory directory path.
     *
     * @return string
     */
    public function getFactoryDirectory()
    {
        return $this->getDatabaseDirectory().'factories/';
    }

    /**
     * Return the seeds directory path.
     *
     * @return string
     */
    public function getSeedDirectory()
    {
        return $this->getDatabaseDirectory().'seeds/';
    }

    /**
     * Make database directory structure if it doesn't exist.
     */
    public function getAllDatabaseDirectories()
    {
        return [
            $this->getDatabaseDirectory(),
            $this->getMigrationDirectory(),
            $this->getFactoryDirectory(),
            $this->getSeedDirectory()
        ];
    }
}
