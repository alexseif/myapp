<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Logic;


use AppBundle\Util\DateRanges;

class BillingCalculator
{

    public function calculateAmountPerMonthHoursPerDay($billingType)
    {
        $fromDate = new \DateTime();
        $fromDate->setDate($fromDate->format('Y'), $fromDate->format('m'), $billingType['billingOn']);
        $fromDate->setTime(0, 0, 0);
        $toDate = new \DateTime();
        $toDate->setDate($toDate->format('Y'), $toDate->format('m'), $billingType['billingOn']);
        $toDate->setTime(23, 23, 59);
        $toDate->modify('+1 month');
        $workingDays = DateRanges::getWorkingDays($fromDate->format('c'), $toDate->format('c'));
        $thisMonthHours = ($workingDays * $billingType['hours']);
        return $billingType['amount'] / $thisMonthHours;

    }

    public function getPricePerUnit($billingType)
    {
        switch ($billingType['amountPer']) {
            case'month':
                switch ($billingType['hoursPer']) {
                    case 'day':
                        $pricePerUnit = $this->calculateAmountPerMonthHoursPerDay($billingType);
                        break;
                }
                break;
        }
        return $pricePerUnit;
    }

}