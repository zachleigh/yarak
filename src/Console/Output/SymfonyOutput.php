<?php

namespace Yarak\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;

class SymfonyOutput extends Output
{
    /**
     * Symfony command output.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Construct.
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Write a message.
     *
     * @param string $message
     */
    public function write($message)
    {
        if ($this->verbosity) {
            $this->output->writeln($message);
        }
    }

    /**
     * Get the Symfony output class.
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}
