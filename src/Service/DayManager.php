<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use DateTime;

/**
 * Description of DayManager.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class DayManager
{
    public function isWorkDay()
    {
        $today = new DateTime();
        if (in_array($today->format('N'), [5, 6])) { // 5 and 6 are weekend days
            return false;
        }

        return true;
    }

    public function isWorkingDay()
    {
        if ($this->isWorkDay()) {
            return 'Yes, it\'s a working day.';
        }

        return 'No, it\'s not a working day.';
    }

    /**
     * @param mixed $date Optional to specify which month start
     *
     * @return DateTime
     */
    public function getMonthStart($date = 'now')
    {
        $monthStart = new DateTime($date);
        if ($monthStart->format('d') < 25) {
            $monthStart->modify('-1 month');
        }
        $monthStart->setTime(0, 0, 0);
        $monthStart->setDate($monthStart->format('Y'), $monthStart->format('m'), 25);

        return $monthStart;
    }

    /**
     * @param mixed $date Optional to specify which month start
     *
     * @return DateTime
     */
    public function getMonthEnd($date = 'now')
    {
        $monthEnd = new DateTime($date);
        if ($monthEnd->format('d') >= 25) {
            $monthEnd->modify('+1 month');
        }
        $monthEnd->setTime(23, 59, 59);
        $monthEnd->setDate($monthEnd->format('Y'), $monthEnd->format('m'), 24);

        return $monthEnd;
    }

    public function getMonthPercentage($date = 'now')
    {
        $monthStart = $this->getMonthStart($date);
        $today = new DateTime();
        $diff = $monthStart->diff($today);

        return round($diff->days / $today->format('t') * 100);
    }
}
