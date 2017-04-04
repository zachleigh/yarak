<?php

namespace Yarak\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class Parser
{
    /**
     * Command to build.
     *
     * @var Command
     */
    protected $command;

    /**
     * Construct.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Set array modeArray value if value contains *.
     *
     * @param string $value
     * @param string $constant
     *
     * @return string
     */
    protected function parseArray($value, $constant)
    {
        if (strpos($value, '*') !== false) {
            $this->modeArray[] = $constant;

            $value = str_replace('*', '', $value);
        }

        return $value;
    }

    /**
     * Parse an argument/option description.
     *
     * @param string $value
     *
     * @return array [argument|option, description]
     */
    protected function parseDescription($value)
    {
        if (strpos($value, ':') !== false) {
            return array_map('trim', explode(':', $value));
        }

        return [$value, null];
    }

    /**
     * Calculate the mode score.
     *
     * @param array  $modeArray
     * @param string $class
     *
     * @return int
     */
    protected function calculateMode(array $modeArray, $class)
    {
        $mode = 0;

        foreach ($modeArray as $constant) {
            $mode = $mode | constant($class.'::'.$constant);
        }

        return $mode;
    }
}
