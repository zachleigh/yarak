<?php

namespace Yarak\Console\Stubs;

use Symfony\Component\Console\Command\Command;

class NullCommand extends Command
{
    protected $name;

    protected $description;

    protected $options = [];

    protected $arguments = [];

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
    }

    public function addArgument($name, $mode = null, $description = '', $default = null)
    {
        $this->arguments[] = [
            'name'        => $name,
            'mode'        => $mode,
            'description' => $description,
            'default'     => $default,
        ];
    }

    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $this->options[] = [
            'name'        => $name,
            'shortcut'    => $shortcut,
            'mode'        => $mode,
            'description' => $description,
            'default'     => $default,
        ];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
