<?php

namespace Yarak\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class ArgumentParser extends Parser
{
    /**
     * Argument name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Array of argument modes to apply.
     *
     * @var array
     */
    protected $modeArray = [];

    /**
     * Argument description.
     *
     * @var null|string
     */
    protected $description = null;

    /**
     * Default arguement value.
     *
     * @var null|string
     */
    protected $default = null;

    /**
     * Parse the given arguement string.
     *
     * @param  string $argument
     */
    public function handle($argument)
    {
        list($argument, $this->description) = $this->parseDescription($argument);

        $argument = $this->parseArray($argument, 'IS_ARRAY');

        $argument = $this->findOptional($argument);

        if (strpos($argument, '=')) {
            list($argument, $this->default) = explode('=', $argument);

            $this->modeArray[] = 'OPTIONAL';
        }

        if (!in_array('OPTIONAL', $this->modeArray)) {
            $this->modeArray[] = 'REQUIRED';
        }

        $this->command->addArgument(
            $argument,
            $this->calculateMode($this->modeArray, InputArgument::class),
            $this->description,
            $this->default
        );
    }

    /**
     * Parse the argument mode.
     *
     * @param  string $argument
     *
     * @return string
     */
    protected function findOptional($argument)
    {
        if (substr($argument, -1) === '?') {
            $this->modeArray[] = 'OPTIONAL';

            return str_replace('?', '', $argument);
        }

        return $argument;
    }
}
