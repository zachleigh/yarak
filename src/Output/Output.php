<?php

namespace Yarak\Output;

interface Output
{
    /**
     * Write a message.
     *
     * @param string $message
     */
    public function write($message);

    /**
     * Write an info message.
     *
     * @param string $message
     */
    public function writeInfo($message);

    /**
     * Write an error message.
     *
     * @param string $message
     */
    public function writeError($message);

    /**
     * Write a comment message.
     *
     * @param string $message
     */
    public function writeComment($message);
}
