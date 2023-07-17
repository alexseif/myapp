<?php

namespace AppBundle\Service;

use AppBundle\Routine\RoutineItem;
use AppBundle\Routine\Workday;
use DateInterval;
use DateTime;

class WhatTodoNow
{
    public function whatTodoNow(): RoutineItem
    {
        $now = new DateTime();
        $workday = new Workday($now);
        $now->add(DateInterval::createFromDateString('2 hours'));
        //What to do now
        $wtn = null;
        foreach ($workday->getItems() as $item) {
            if (is_null($wtn)) {
                $wtn = $item;
            }
            if ($now > $item->getStart()) {
                $wtn = $item;
            }
        }

        return $wtn;
    }
}
