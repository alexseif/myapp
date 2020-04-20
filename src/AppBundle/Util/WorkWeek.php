<?php

namespace AppBundle\Util;

/*
 * The following content was designed & implemented under AlexSeif.com
 */

/**
 * Utility class for WorkWeek Operations
 *
 * @author Alex Seif <me@alexseif.com>
 */
class WorkWeek
{

  private static $workWeek = [
    'Friday' => 0,
    'Saturday' => 4,
    'Sunday' => 8,
    'Monday' => 8,
    'Tuesday' => 8,
    'Wednesday' => 8,
    'Thursday' => 8,
  ];

  public static function getWorkWeek()
  {
    return self::$workWeek;
  }

  public static function getDayHours($day)
  {
    if (key_exists($day, self::$workWeek)) {
      return self::$workWeek[$day];
    }
    return null;
  }

}
