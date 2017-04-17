<?php

namespace Yarak\Config;

use Phalcon\DI;
use Yarak\Exceptions\InvalidConfig;
use Phalcon\Config as PhalconConfig;

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
     * Phalcon config instance.
     *
     * @var PhalconConfig
     */
    protected $config;

    /**
     * The original config array.
     *
     * @var array
     */
    protected $original;

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
     *
     * @param array $configArray
     */
    private function __construct()
    {
    }

    /**
     * Get config values by key from PhalconConfig.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        try {
            return $this->config->$key;
        } catch (\Exception $e) {
            return $this->getDefault($key);
        }
    }

    /**
     * Get instance of self with config array set.
     *
     * @param array $configArray
     *
     * @return Config
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set config using config data in di or passed data.
     *
     * @param array|string $userConfig If string, config path. If array, data.
     * @param bool         $merge      If true, merge given config array into current.
     */
    public function setConfig($userConfig = null, $merge = true)
    {
        $this->config = \Phalcon\Di::getDefault()->get('config');

        if (is_string($userConfig)) {
            $this->config = $this->getNested(explode('.', $userConfig));
        } elseif (is_array($userConfig)) {
            $userConfig = new PhalconConfig($userConfig);

            if ($merge === true) {
                $this->merge($userConfig);
            } else {
                $this->config = $userConfig;
            }
        }

        $this->original = $this->toArray();

        return $this;
    }

    /**
     * Get a value by key from config.
     *
     * @param string|array $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * Get nested values.
     *
     * @param array $keyArray
     *
     * @return mixed
     */
    public function getNested(array $keyArray)
    {
        $current = $this->config;

        foreach ($keyArray as $key) {
            if ($current[$key] === null) {
                return $this->getDefault($key);
            }

            $current = $current->$key;
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
        $current = $this->config;

        foreach ($this->makeArray($value) as $configItem) {
            if ($current[$configItem] === null) {
                $current = $this->getDefault($configItem);

                break;
            }

            $current = $current->$configItem;
        }

        return $current !== null;
    }

    /**
     * Get a setting's default value.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getDefault($key)
    {
        if (array_key_exists($key, self::DEFAULTS)) {
            return self::DEFAULTS[$key];
        }

        return;
    }

    /**
     * Set an item in the config.
     *
     * @param mixed $keys
     * @param mixed $value
     */
    public function set($keys, $value)
    {
        $temp = &$this->config;

        $keys = $this->makeArray($keys);

        $count = count($keys);

        foreach ($keys as $index => $key) {
            if ($count === $index + 1) {
                return $temp[$key] = $value;
            } elseif ($temp[$key] === null) {
                $temp[$key] = new PhalconConfig();
            }

            $temp = &$temp[$key];
        }
    }

    /**
     * Remove an item from the config.
     *
     * @param mixed $keys
     */
    public function remove($keys)
    {
        $temp = &$this->config;

        $keys = $this->makeArray($keys);

        $count = count($keys);

        foreach ($keys as $key) {
            if ($key === $keys[$count - 1]) {
                unset($temp[$key]);
            } else {
                $temp = &$temp[$key];
            }
        }
    }

    /**
     * Set the config array to its original values.
     */
    public function refresh()
    {
        $this->config = $this->config->__set_state($this->original);
    }

    /**
     * Return the config array.
     *
     * @return array
     */
    public function toArray()
    {
        if (is_string($this->config)) {
            return $this->config;
        }

        return $this->config->toArray();
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

    /**
     * Validate that a setting exists.
     *
     * @param array $settings
     *
     * @throws InvalidConfig
     */
    public function validate(array $settings)
    {
        if (!$this->has($settings)) {
            throw InvalidConfig::configValueNotFound(implode(' -> ', $settings));
        }
    }

    /**
     * Merge the given config with the current one.
     *
     * @param PhalconConfig $config
     */
    public function merge(PhalconConfig $config)
    {
        $this->config = $this->config->merge($config);
    }

    /**
     * Return the config key count.
     *
     * @return int
     */
    public function count()
    {
        return $this->config->count();
    }
}
