<?php

namespace Yarak\Console\Output;

abstract class Output
{
    /**
     * Output verbosity.
     *
     * @var bool
     */
    protected $verbosity = true;

    /**
     * Write a message.
     *
     * @param string $message
     */
    abstract public function write($message);

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

    /**
     * Set the output verbosity.
     *
     * @param bool $verbosity
     *
     * @return $this
     */
    public function setVerbosity($verbosity)
    {
        $this->verbosity = $verbosity;

        return $this;
    }
}
