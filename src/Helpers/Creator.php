<?php

namespace Yarak\Helpers;

use Yarak\Config\Config;
use Yarak\Console\Output\Output;

abstract class Creator
{
    use Filesystem;

    /**
     * Yarak config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Output strategy.
     *
     * @var Output
     */
    protected $output;

    /**
     * Construct.
     *
     * @param Config $config
     * @param Output $output
     */
    public function __construct(Config $config, Output $output)
    {
        $this->config = $config;
        $this->output = $output;
    }

    /**
     * Guess a namespace based on a path.
     *
     * @param string $path
     *
     * @return string|null
     */
    protected function guessNamespace($path)
    {
        if (defined('APP_PATH')) {
            $appPathArray = explode('/', APP_PATH);

            $relativePath = array_diff(explode('/', $path), $appPathArray);

            array_unshift($relativePath, array_pop($appPathArray));

            $relativePath = array_map('ucfirst', $relativePath);

            return implode('\\', $relativePath);
        }
    }

    /**
     * Set the namespace in the command stub.
     *
     * @param string $stub
     * @param string $namespace
     *
     * @return string
     */
    protected function setNamespace($stub, $namespace = null)
    {
        if ($namespace !== null) {
            return str_replace(
                'NAMESPACE',
                "\nnamespace {$namespace};\n",
                $stub
            );
        }

        return str_replace('NAMESPACE', '', $stub);
    }
}
