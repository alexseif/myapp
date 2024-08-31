<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace App\Logic;

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

    public function setWorkingDays(): void
    {
        $this->workingDays = 22;
    }

    public function calculateAmountPerMonthHoursPerDay(): void
    {
        $thisMonthHours = ($this->workingDays * $this->billingType['hours']);
        $this->pricePerUnit = $this->billingType['amount'] / $thisMonthHours;
    }

    public function getPricePerUnit()
    {
        return $this->pricePerUnit;
    }
}
