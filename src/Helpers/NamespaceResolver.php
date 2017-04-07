<?php

namespace Yarak\Helpers;

use Yarak\Config\Config;

class NamespaceResolver
{
    /**
     * Resolve namespace for the given root path, add additional values.
     *
     * @param string $root
     * @param string $additional
     *
     * @return string|null
     */
    public static function resolve($root, $additional = '')
    {
        $config = Config::getInstance();

        if ($config->has(['namespaces', $root])) {
            $namespace = $config->get(['namespaces', $root]);
        } else {
            $namespace = self::guessNamespace($root);
        }

        if ($namespace !== null && $additional) {
            return Str::append($namespace, '\\').ucfirst($additional);
        }

        return $namespace;
    }

    /**
     * Guess the namespace for the given dir path.
     *
     * @param string $dir
     *
     * @return string|null
     */
    public static function guessNamespace($dir)
    {
        if (!defined('APP_PATH')) {
            return;
        }

        $config = Config::getInstance();

        $method = 'get'.ucfirst($dir).'Directory';

        $path = Config::getInstance()->$method();

        $appPathArray = explode('/', APP_PATH);

        $relativePath = array_diff(explode('/', $path), $appPathArray);

        if (($root =$config->get(['namespaces', 'root'])) !== null) {
            array_unshift($relativePath, $root);
        } else {
            array_unshift($relativePath, array_pop($appPathArray));
        }
        
        return implode('\\', array_map('ucfirst', $relativePath));
    }
}
