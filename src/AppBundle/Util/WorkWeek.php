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
    'Saturday' => 2,
    'Sunday' => 6,
    'Monday' => 6,
    'Tuesday' => 6,
    'Wednesday' => 6,
    'Thursday' => 6,
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
