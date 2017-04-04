<?php

namespace Yarak\Console\Input;

abstract class Input
{
    /**
     * Input.
     *
     * @var string
     */
    protected $input;

    /**
     * Original input string.
     *
     * @var string
     */
    protected $original;

    /**
     * Input name.
     *
     * @var string
     */
    protected $name;

    /**
     * Input description.
     *
     * @var null|string
     */
    protected $description;

    /**
     * Array of input modes to apply.
     *
     * @var array
     */
    protected $modeArray = [];

    /**
     * Argument mode.
     *
     * @var int
     */
    protected $mode = 0;

    /**
     * Construct.
     *
     * @param string $input
     */
    public function __construct($input)
    {
        $this->input = $input;
        $this->original = $input;
    }

    /**
     * Parse the set input string.
     *
     * @return $this
     */
    abstract public function parse();

    /**
     * Get the input attributes.
     *
     * @return array
     */
    abstract public function getAttributes();

    /**
     * Set array modeArray value if value contains *.
     *
     * @param string $constant
     *
     * @return $this
     */
    protected function setArray($constant)
    {
        if (strpos($this->input, '*') !== false) {
            $this->modeArray[] = $constant;

            $this->input = str_replace('*', '', $this->input);
        }

        return $this;
    }

    /**
     * Parse an argument/option description.
     *
     * @return $this
     */
    protected function setDescription()
    {
        if (strpos($this->input, ':') !== false) {
            $inputArray = array_map('trim', explode(':', $this->input));

            $this->input = $inputArray[0];

            $this->description = $inputArray[1];
        }

        return $this;
    }

    /**
     * Calculate the mode score.
     *
     * @param string $class
     *
     * @return $this
     */
    protected function calculateMode($class)
    {
        foreach ($this->modeArray as $constant) {
            $this->mode = $this->mode | constant($class.'::'.$constant);
        }

        return $this;
    }

    /**
     * Set the input name as the input value.
     *
     * @return $this
     */
    protected function setName()
    {
        $this->name = $this->input;

        return $this;
    }
}
