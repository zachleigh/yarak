<?php

namespace Yarak\Output;

class Logger extends Output
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
