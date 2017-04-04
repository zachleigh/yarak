<?php

namespace Yarak\Console\Command;

use Symfony\Component\Console\Command\Command;

class SignatureParser
{
    /**
     * The command to build.
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
     * Parse the command signature.
     *
     * @param string $signature
     */
    public function parse($signature)
    {
        $this->setName($signature);

        $argumentsOptions = $this->extractArgumentsOptions($signature);

        foreach ($argumentsOptions as $value) {
            if (substr($value, 0, 2) !== '--') {
                $parser = new ArgumentParser($this->command);
            } else {
                $parser = new OptionParser($this->command);

                $value = trim($value, '--');
            }

            $parser->handle($value);
        }
    }

    /**
     * Set the command name.
     *
     * @param string $signature
     */
    protected function setName($signature)
    {
        $this->command->setName(preg_split('/\s+/', $signature)[0]);
    }

    /**
     * Extract arguments and options from signature.
     *
     * @param string $signature
     *
     * @return array
     */
    protected function extractArgumentsOptions($signature)
    {
        preg_match_all('/{(.*?)}/', $signature, $argumentsOption);

        return array_map(function ($item) {
            return trim($item, '{}');
        }, $argumentsOption[1]);
    }
}
