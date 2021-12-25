<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace AppBundle\Logic;

use AppBundle\Util\DateRanges;
use DateTime;

class BillingCalculator
{
    protected $workingDays;
    protected $billingType;
    protected $pricePerUnit;

    public function __construct($billingType)
    {
        $this->billingType = $billingType;
        $this->setWorkingDays();

        if (('month' === $this->billingType['amountPer']) && 'day' === $this->billingType['hoursPer']) {
            $this->calculateAmountPerMonthHoursPerDay();
        }
    }

    public function setWorkingDays()
    {
        $fromDate = new DateTime();
        $fromDate->setDate($fromDate->format('Y'), $fromDate->format('m'), $this->billingType['billingOn']);
        $fromDate->setTime(0, 0, 0);
        $toDate = new DateTime();
        $toDate->setDate($toDate->format('Y'), $toDate->format('m'), $this->billingType['billingOn']);
        $toDate->setTime(23, 23, 59);
        $toDate->modify('+1 month');
        $this->workingDays = DateRanges::getWorkingDays($fromDate->format('c'), $toDate->format('c'));
    }

    public function calculateAmountPerMonthHoursPerDay()
    {
        $thisMonthHours = ($this->workingDays * $this->billingType['hours']);
        $this->pricePerUnit = $this->billingType['amount'] / $thisMonthHours;
    }

    public function getPricePerUnit()
    {
        return $this->pricePerUnit;
    }
}
