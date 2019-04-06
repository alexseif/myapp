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
    $start = new \DateTime($startDate);
    if ($setDayTo > 0 && $setDayTo < 32) {
      $start->setDate($start->format("Y"), $start->format("m"), $setDayTo);
    }
    $end = new \DateTime($endDate);
    $interval = \DateInterval::createFromDateString('1 month');
    $period = new \DatePeriod($start, $interval, $end);
    $dateArray = array();

    $index = 0;
    foreach ($period as $dt) {
      $dateArray[$index]['start'] = $dt->format('Y-m-d');
      $dateArray[$index++]['end'] = $dt->format('Y-m-t');
    }
    return $dateArray;
  }

}
