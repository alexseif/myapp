<?php

namespace App\Util;

/*
 * The following content was designed & implemented under AlexSeif.com
 */

/**
 * Utility class for Formatting.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Formatting
{

    public static function number($number): string
    {
        return number_format(round($number));
    }

}
