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
     * Default setting values.
     *
     * @var array
     */
    const DEFAULTS = [
        'migratorType'        => 'fileDate',
        'migrationRepository' => 'database',
    ];

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

            if (!isset($current[$configItem])) {
                return $this->getDefault($configItem);
            } else {
                $current = $current[$configItem];
            }
        }

        return $current;
    }

    /**
     * Get a setting's default value.
     *
     * @param string $value
     *
     * @return mixed|null
     */
    public function getDefault($value)
    {
        if (array_key_exists($value, self::DEFAULTS)) {
            return self::DEFAULTS[$value];
        }

        return null;
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
            $this->getDatabaseDirectory(),
            $this->getMigrationDirectory(),
            $this->getFactoryDirectory(),
            $this->getSeedDirectory(),
        ];
    }
}
