<?php

namespace Yarak\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class OptionParser extends Parser
{
    /**
     * Option name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Option shortcut.
     *
     * @var null|string
     */
    protected $shortcut = null;

    /**
     * Array of option modes to apply.
     *
     * @var array
     */
    protected $modeArray = [];

    /**
     * Option description.
     *
     * @var null|string
     */
    protected $description = null;

    /**
     * Default option value.
     *
     * @var null|string
     */
    protected $default = null;

    /**
     * Parse the given option string.
     *
     * @param string $option
     */
    public function handle($option)
    {
        list($option, $this->description) = $this->parseDescription($option);

        $option = $this->parseArray($option, 'VALUE_IS_ARRAY');

        if (strpos($option, '=') !== false) {
            $option = $this->parseValue($option);
        }

        $this->modeArray = empty($this->modeArray) ? ['VALUE_NONE'] : $this->modeArray;

        if (strpos($option, '|') !== false) {
            list($this->shortcut, $option) = explode('|', $option);
        }

        $this->command->addOption(
            $option,
            $this->shortcut,
            $this->calculateMode($this->modeArray, InputOption::class),
            $this->description,
            $this->default
        );
    }

    /**
     * Parse option value and value default.
     *
     * @param string $option
     *
     * @return string
     */
    protected function parseValue($option)
    {
        if (substr($option, -1) === '=') {
            $option = str_replace('=', '', $option);

            $this->modeArray[] = 'VALUE_REQUIRED';
        } else {
            list($option, $this->default) = explode('=', $option);

            $this->modeArray[] = 'VALUE_OPTIONAL';
        }

        return $option;
    }
}
