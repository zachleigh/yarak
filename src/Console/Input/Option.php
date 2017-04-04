<?php

namespace Yarak\Console\Input;

use Symfony\Component\Console\Input\InputOption;

class Option extends Input
{
    /**
     * Option shortcut.
     *
     * @var null|string
     */
    protected $shortcut = null;

    /**
     * Default option value.
     *
     * @var null|string
     */
    protected $default = null;

    /**
     * Parse the set input string.
     *
     * @return $this
     */
    public function parse()
    {
        return $this->setDescription()
            ->setArray('VALUE_IS_ARRAY')
            ->setValue()
            ->setEmptyMode()
            ->setShortcut()
            ->calculateMode(InputOption::class)
            ->setName();
    }

    /**
     * Parse option value and value default.
     *
     * @return $this
     */
    protected function setValue()
    {
        if (strpos($this->input, '=') !== false) {
            if (substr($this->input, -1) === '=') {
                $this->input = str_replace('=', '', $this->input);

                $this->modeArray[] = 'VALUE_REQUIRED';
            } else {
                list($this->input, $this->default) = explode('=', $this->input);

                $this->modeArray[] = 'VALUE_OPTIONAL';
            }
        }

        return $this;
    }

    /**
     * If mode is empty, set VALUE_NONE.
     *
     * @return $this
     */
    protected function setEmptyMode()
    {
        $this->modeArray = empty($this->modeArray) ? ['VALUE_NONE'] : $this->modeArray;

        return $this;
    }

    /**
     * Set option shortcut.
     *
     * @return $this
     */
    protected function setShortcut()
    {
        if (strpos($this->input, '|') !== false) {
            list($this->shortcut, $this->input) = explode('|', $this->input);
        }

        return $this;
    }

    /**
     * Get the option attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return [
            'name'        => $this->name,
            'shortcut'    => $this->shortcut,
            'mode'        => $this->mode,
            'description' => $this->description,
            'default'     => $this->default,
        ];
    }
}
