<?php

namespace Yarak\Helpers;

class Str
{
    /*
    | This class is adapted from Laravel's Str class:
    | https://github.com/illuminate/support/blob/master/Str.php
    */

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
}
