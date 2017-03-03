<?php

use Phalcon\Debug\Dump;

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     */
    function dd()
    {
        array_map(function ($x) {
            $string = (new Dump(null, true))->variable($x);

            echo PHP_SAPI == 'cli' ? strip_tags($string).PHP_EOL : $string;
        }, func_get_args());

        die(1);
    }
}
