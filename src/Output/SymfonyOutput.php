<?php

namespace Yarak\Output;

use Symfony\Component\Console\Output\OutputInterface;

class SymfonyOutput implements Output
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

    /**
     * Write an info message.
     *
     * @param string $message
     */
    public function writeInfo($message)
    {
        $this->write("<info>{$message}</info>");
    }

    /**
     * Write an error message.
     *
     * @param string $message
     */
    public function writeError($message)
    {
        $this->write("<error>{$message}</error>");
    }

    /**
     * Write a comment message.
     *
     * @param string $message
     */
    public function writeComment($message)
    {
        $this->write("<comment>{$message}</comment>");
    }
}
