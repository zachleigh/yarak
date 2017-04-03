<?php

namespace Yarak\Helpers;

class Str
{
    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Create StudlyCase string from _ separated string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function studly($value)
    {
        static $studlyCache = [];

        $key = $value;

        if (isset($studlyCache[$key])) {
            return $studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Append a character to a string if it doesn't already exist.
     *
     * @param string $value
     * @param string $char
     *
     * @return string
     */
    public static function append($value, $char)
    {
        if (substr($value, -1) !== $char) {
            $value .= $char;
        }

        return $value;
    }
}
