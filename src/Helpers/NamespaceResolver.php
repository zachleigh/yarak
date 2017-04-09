<?php

namespace Yarak\Helpers;

use Yarak\Config\Config;

class NamespaceResolver
{
    /**
     * Resolve namespace for the given dir path, add additional values.
     *
     * @param string $dir
     * @param string $additional
     *
     * @return string|null
     */
    public static function resolve($dir, $additional = '')
    {
        $config = Config::getInstance();

        if ($config->has(['namespaces', $dir])) {
            $namespace = $config->get(['namespaces', $dir]);
        } elseif ($config->has(['application', $dir.'Dir'])) {
            $namespace = self::resolveFromRegisteredDir($dir);
        } else {
            $namespace = self::resolveFromRelativePath($dir);
        }

        if ($namespace !== null && $additional) {
            return Str::append($namespace, '\\').ucfirst($additional);
        }

        return $namespace;
    }

    /**
     * Resolve a namespace from a registered directory.
     *
     * @param string $dir
     *
     * @return string
     */
    public static function resolveFromRegisteredDir($dir)
    {
        $config = Config::getInstance();

        $method = 'get'.ucfirst($dir).'Directory';

        if (method_exists($config, $method)) {
            return self::resolveFromAbsolutePath($config->$method());
        }

        return null;
    }

    /**
     * Reslove a namespace from a path relative to app root directory.
     *
     * @param string $path
     *
     * @return string
     */
    public static function resolveFromRelativePath($path)
    {
        $pathArray = array_filter(explode(DIRECTORY_SEPARATOR, $path));

        array_unshift($pathArray, self::getRootNamespace());

        return implode('\\', array_map('ucfirst', $pathArray));
    }

    /**
     * Resolve a namespace from an absolute path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function resolveFromAbsolutePath($path)
    {
        $config = Config::getInstance();

        $appPathArray = array_filter(explode('/', $config->getAppPath()));

        $relativePath = array_diff(
            array_filter(explode(DIRECTORY_SEPARATOR, $path)),
            $appPathArray
        );

        array_unshift($relativePath, self::getRootNamespace());

        return implode('\\', array_map('ucfirst', $relativePath));
    }

    /**
     * Get the app root namespace.
     *
     * @return string
     */
    public static function getRootNamespace()
    {
        $config = Config::getInstance();

        if ($config->has(['namespaces', 'root'])) {
            return $config->get(['namespaces', 'root']);
        }

        return 'App';
    }
}
