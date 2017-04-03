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
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;

        $this->config = Config::getInstance();
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

    /**
     * Output nothing created message if no all bools are false.
     *
     * @param array $bools
     */
    protected function outputNothingCreated(array $bools)
    {
        foreach ($bools as $bool) {
            if ($bool) {
                return;
            }
        }

        $this->output->writeComment(
            'Nothing created. All directories and files already exist.'
        );
    }
}
