<?php

namespace Yarak\Helpers;

trait Logger
{
    /**
     * Log of info/error messages.
     *
     * @var array
     */
    protected $log = [];

    /**
     * Log a message.
     *
     * @param string $message
     */
    public function log($message)
    {
        $this->log[] = $message;
    }

    /**
     * Return the object log.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }
}
