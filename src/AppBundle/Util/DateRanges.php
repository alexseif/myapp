<?php

namespace AppBundle\Util;

/*
 * The following content was designed & implemented under AlexSeif.com
 */

/**
 * Utility class for DateRanges Operations
 *
 * @author Alex Seif <me@alexseif.com>
 */
class DateRanges
{

  /**
   * Function to populate months between 2 dates 
   * 
   * @param string $startDate 
   * @param string $endDate
   * @param int $setDayTo Set day of month (for eg. 01/12) if set to 0 the dates use the initial date
   * @return array Array of months between $startDate & $endDate
   */
  public static function populateMonths($startDate, $endDate, $setDayTo = 0)
  {
    $begin = new \DateTime($startDate);
    if ($setDayTo > 0 && $setDayTo < 32) {
      $begin->setDate($begin->format("Y"), $begin->format("m"), 1);
    }
    $end = new \DateTime($endDate);
    $interval = \DateInterval::createFromDateString('1 month');
    $period = new \DatePeriod($begin, $interval, $end);
    $dateArray = array();

    foreach ($period as $dt) {
      $dateArray[] = $dt;
    }
    return $dateArray;
  }

}
