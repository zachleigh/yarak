<?php

namespace Yarak\Commands;

use Symfony\Component\Console\Command\Command;

class YarakCommand extends Command
{
    /**
     * Application config.
     *
     * @var array
     */
    protected $configArray;

    /**
     * Construct.
     *
     * @param array $configArray
     */
    public function __construct(array $configArray)
    {
        parent::__construct();

        $this->configArray = $configArray;
    }
}
