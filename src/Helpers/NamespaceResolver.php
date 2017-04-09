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

            return Str::appendWith($namespace, ucfirst($additional), '\\');
        } elseif ($config->has(['application', $dir.'Dir'])) {
            return self::resolveFromRegisteredDir($dir, $additional);
        } else {
            return self::resolveFromRelativePath($dir, $additional);
        }
    }

    /**
     * Resolve a namespace from a registered directory.
     *
     * @param string $dir
     * @param string $additional
     *
     * @return string
     */
    public static function resolveFromRegisteredDir($dir, $additional)
    {
        $config = Config::getInstance();

        $method = 'get'.ucfirst($dir).'Directory';

        if (method_exists($config, $method)) {
            return self::resolveFromAbsolutePath($config->$method(), $additional);
        }

        return null;
    }

    /**
     * Reslove a namespace from a path relative to app root directory.
     *
     * @param string $path
     * @param string $additional
     *
     * @return string
     */
    public static function resolveFromRelativePath($path, $additional)
    {
        $pathArray = array_filter(explode(DIRECTORY_SEPARATOR, $path));

        array_unshift($pathArray, self::getRootNamespace());

        $namespace = implode('\\', array_map('ucfirst', $pathArray));

        return Str::appendWith($namespace, ucfirst($additional), '\\');
    }

    /**
     * Resolve a namespace from an absolute path.
     *
     * @param string $path
     * @param string $additional
     *
     * @return string
     */
    public static function resolveFromAbsolutePath($path, $additional)
    {
        $appPathArray = array_filter(
            explode('/', Config::getInstance()->getAppPath())
        );

        $relativePath = array_diff(
            array_filter(explode(DIRECTORY_SEPARATOR, $path)),
            $appPathArray
        );

        array_unshift($relativePath, self::getRootNamespace());

        $namespace = implode('\\', array_map('ucfirst', $relativePath));

        return Str::appendWith($namespace, ucfirst($additional), '\\');
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
