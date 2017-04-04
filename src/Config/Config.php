<?php

namespace Yarak\Config;

use Phalcon\DI;

class Config
{
    use PathHelpers;

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
     *
     * @param array $configArray
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
                $configArray = DI::getDefault()
                    ->getShared('yarak')
                    ->getConfigArray();
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
        $current = $this->configArray;

        foreach ($this->makeArray($value) as $configItem) {
            if (!isset($current[$configItem])) {
                return $this->getDefault($configItem);
            }

            $current = $current[$configItem];
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
        return !($this->get($value) === null);
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
     * Set an item in the config.
     *
     * @param mixed $keys
     * @param mixed $value
     */
    public function set($keys, $value)
    {
        $temp = &$this->configArray;

        foreach ($this->makeArray($keys) as $key) {
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
     * Return the config array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->configArray;
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
            return [$value];
        }

        return $value;
    }
}
