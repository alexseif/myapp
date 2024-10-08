<?php

namespace App\Util;

/*
 * The following content was designed & implemented under AlexSeif.com
 */

use DateInterval;
use DatePeriod;
use DateTime;
use DOMDocument;

/**
 * Utility class for DateRanges Operations.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class DateRanges
{

    /**
     * Function to populate months between 2 dates.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|string $setDayTo Set day of month (for eg. 01/12) if set to 0 the dates use the initial date if int
     * between 0 and 32 set date, if string then modify is run on end of month ("+2"|"-5")
     *
     * @return array Array of months between $startDate & $endDate
     */
    public static function populateMonths(
      string $startDate,
      string $endDate,
      int|string $setDayTo = 0
    ): array {
        $start = self::getMonthStart($startDate);
        $start->setDate($start->format('Y'), $start->format('m'), $setDayTo);
        $end = self::getMonthEnd($endDate);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $dateArray = [];

        $index = 0;
        foreach ($period as $dt) {
            $dateArray[$index]['start'] = $dt->format('Y-m-d');
            if (($setDayTo > 0 && $setDayTo < 32) || is_string($setDayTo)) {
                $dt->modify('+1 month')
                  ->modify('-1 day');
                $dateArray[$index++]['end'] = $dt->format('Y-m-d');
            } else {
                $dateArray[$index++]['end'] = $dt->format('Y-m-t');
            }
        }

        return $dateArray;
    }

    /**
     * @TODO remove and usages migrating to new function
     * Returns number of working days
     * https://stackoverflow.com/a/19221403/1030170
     *
     * @param DateTime $from
     * @param DateTime $to
     *
     * @return int
     */
    public static function numberOfWorkingDays(DateTime $from, DateTime $to): int
    {
        $workingDays = [1, 2, 3, 4, 7]; // date format = N (1 = Monday, ...)
        $holidayDays = [
          '*-12-25',
          '*-01-01',
          '*-10-29',
        ]; // variable and fixed holidays
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) {
                continue;
            }
            if (in_array($period->format('Y-m-d'), $holidayDays)) {
                continue;
            }
            if (in_array($period->format('*-m-d'), $holidayDays)) {
                continue;
            }
            ++$days;
        }

        return $days - 1;
    }

    /**
     * The function returns the no. of business days between two dates and it skips the holidays.
     *
     * @param string $startDate
     * @param string $endDate
     *
     * @return int
     */
    public static function getWorkingDays(string $startDate, string $endDate): int
    {
        $holidays = self::getHolidays('egypt');

        $begin = strtotime($startDate);
        $end = strtotime($endDate);
        if ($begin > $end) {
            echo 'startdate is in the future! <br />';

            return 0;
        }

        $no_days = 0;
        $weekends = 0;
        while ($begin <= $end) {
            ++$no_days; // no of days in the given interval
            $what_day = date('N', $begin);
            if (in_array($what_day, [5, 6])) { // 6 and 7 are weekend days
                ++$weekends;
            }
            $begin += 86400; // +1 day
        }
        $working_days = $no_days - $weekends;

        $holiday_days = 0;
        $beginDT = new DateTime($startDate);
        $endDT = new DateTime($endDate);
        foreach ($holidays as $holiday) {
            $dt = new DateTime($holiday[0]);
            $dtTimestamp = $dt->getTimestamp();
            if (($dtTimestamp >= $beginDT->getTimestamp())
              && ($dtTimestamp <= $endDT->getTimestamp())
              && (in_array($dt->format('N'), [5, 6], true))) {
                ++$holiday_days;
            }
        }
        $working_days -= $holiday_days;

        return $working_days;
    }

    /**
     * Get public holidays for a given month.
     *
     * @param string $country
     *
     * @return array
     */
    public static function getHolidays(string $country): array
    {
        //Url of Site with list
        $url = 'https://www.timeanddate.com/holidays/' . $country . '/';
        //Use curl to get the page
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);
        $dom = new DOMDocument();
        // The @ before the method call suppresses any warnings that
        // loadHTML might throw because of invalid HTML in the page.
        @$dom->loadHTML($html);
        $holidays = [];
        $items = $dom->getElementsByTagName('tr');

        if (!function_exists('AppBundle\Util\tdrows')) {
            function tdrows($elements)
            {
                $str = '';
                foreach ($elements as $element) {
                    $str .= $element->nodeValue . ', ';
                }
                //This pplaces the items into an array
                $tempArray = explode(',', $str);
                //This gets rid of empty array elements
                unset($tempArray[4], $tempArray[5]);

                return $tempArray;
            }
        }

        foreach ($items as $node) {
            if (count($node->childNodes) > 1) {
                $holidays[] = tdrows($node->childNodes);
            }
        }
        //The first and second items in the array were the titles of the table and a blank row
        //so we unset them
        unset($holidays[0]);
        //    unset($holidays[1]);
        //    unset($holidays[2]);
        //then reindex the array
        return array_values($holidays);
    }

    /**
     *
     */
    public static function getMonthStart(
      string $date = 'now',
      $billedOn = 25
    ): DateTime {
        $monthStart = new DateTime($date);
        if ($monthStart->format('d') < $billedOn) {
            $monthStart->modify('-1 month');
        }
        $monthStart->setTime(0, 0);
        $monthStart->setDate(
          $monthStart->format('Y'),
          $monthStart->format('m'),
          $billedOn
        );

        return $monthStart;
    }

    /**
     *
     * @return DateTime
     */
    public static function getMonthEnd(string $date = 'now'): DateTime
    {
        $monthEnd = new DateTime($date);
        if ($monthEnd->format('d') >= 25) {
            $monthEnd->modify('+1 month');
        }
        $monthEnd->setTime(23, 59, 59);
        $monthEnd->setDate($monthEnd->format('Y'), $monthEnd->format('m'), 24);

        return $monthEnd;
    }

    public static function getDatesFromRange(
      DateTime $start,
      DateTime $end
    ): array {
        // Declare an empty array
        $array = [];

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $end->add($interval);

        $period = new DatePeriod($start, $interval, $end);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date;
        }

        // Return the array elements
        return $array;
    }

    /**
     * @return array of days in a working week starting Sunday
     */
    public static function getWorkWeek(): array
    {
        $day_start = date('d', strtotime('next Sunday')); // get next Sunday
        for ($x = 0; $x < 5; ++$x) {
            $week_days[] = date(
              'l',
              mktime(0, 0, 0, date('m'), $day_start + $x, date('y'))
            );
        } // create weekdays array.

        return $week_days;
    }

}
