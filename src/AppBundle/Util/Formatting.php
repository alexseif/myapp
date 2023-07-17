<?php

namespace AppBundle\Util;

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
    public static function number($number)
    {
        return number_format(round($number), 0);
    }
}
