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
        'migratorType' => 'fileDate',
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
            if (empty($configArray)) {
                $di = \Phalcon\DI::getDefault();

                $configArray = $di->getShared('yarak')->getConfig();
            }

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
        $value = $this->makeArray($value);

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
     * Return true if config array has given value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function has($value)
    {
        if ($this->get($value) === null) {
            return false;
        }

        return true;
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
     * Set an item in the config.
     *
     * @param mixed $keys
     * @param mixed $value
     */
    public function set($keys, $value)
    {
        $keys = $this->makeArray($keys);

        $temp = &$this->configArray;

        foreach ($keys as $key) {
            $temp = &$temp[$key];
        }

        $temp = $value;
    }

    /**
     * Remove an item from the config.
     *
     * @param mixed $keys
     */
    public function remove($keys)
    {
        $keys = $this->makeArray($keys);

        $temp = &$this->configArray;

        foreach ($keys as $key) {
            if ($key === $keys[count($keys) - 1]) {
                unset($temp[$key]);
            } else {
                $temp = &$temp[$key];
            }
        }
    }

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
            $this->getDatabaseDirectory(),
            $this->getMigrationDirectory(),
            $this->getFactoryDirectory(),
            $this->getSeedDirectory(),
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
     * Return the config array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAll();
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

    /**
     * Make a variable an array if not one already.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function makeArray($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return $value;
    }
}
