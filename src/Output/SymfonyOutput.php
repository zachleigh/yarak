<?php

namespace Yarak\Output;

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
        $this->output->writeln($message);
    }
}
