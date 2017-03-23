<?php

namespace Yarak\Output;

class Logger implements Output
{
    /**
     * Log of received messages.
     *
     * @var array
     */
    protected $log = [];

    /**
     * Write a message.
     *
     * @param string $message
     */
    public function write($message)
    {
        $this->log[] = $message;
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

    /**
     * Return the log array.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Return true if log contains message.
     *
     * @param  string  $message
     *
     * @return boolean
     */
    public function hasMessage($message)
    {
        return in_array($message, $this->getLog());
    }
}