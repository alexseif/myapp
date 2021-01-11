<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

//TODO: blank balance throws an issue

namespace AppBundle\Logic;

use AppBundle\Entity\Balance;
use AppBundle\Model\RecurringTransactions;
use AppBundle\Model\Sheet;

/**
 * Description of BalanceEnumeration
 *
 * @author Alex Seif <me@alexseif.com>
 */
class BalanceEnumeration
{

    /**
     *
     * @var \DateTime today
     */
    private $today;

    /**
     *
     * @var \DateTime startDate
     */
    private $startDate;

    /**
     *
     * @var \DateTime endDate
     */
    private $endDate;

    /**
     *
     * @var Balance balance
     */
    private $balance;

    /**
     *
     * @var \DatePeriod dateRange
     */
    private $dateRange;

    /**
     *
     * @var array recurringTransactions
     */
    private $recurringTransactions = array();

    /**
     *
     * @var int initial
     */
    private $initial = 0;

    /**
     *
     * @var int dailyAverage
     */
    private $dailyAverage = 200;

    /**
     *
     * @var int total
     */
    private $total = 0;

    /**
     *
     * @var array sheet
     */
    private $sheet = array();

    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
        $this->today = new \DateTime();
        // Begining of month
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
        $this->startDate->setDate($this->today->format('Y'), $this->today->format('m'), 1);
        $this->endDate->setDate($this->today->format('Y'), $this->today->format('m'), 1);
        // End of month
        $this->endDate->add(new \DateInterval('P1M'));

        $this->populateRecurringTransactions();
        $this->calculateInitial();
        $this->enumerateSheet();
    }

    public function populateRecurringTransactions()
    {
        $this->addRecurringTransaction('Mediatemple', '400', \DateTime::createFromFormat('Y-m-d', '2018-11-18'));
        $this->addRecurringTransaction('Vodafone', '250', \DateTime::createFromFormat('Y-m-d', '2018-11-1'));
        $this->addRecurringTransaction('EKA', '-2000', \DateTime::createFromFormat('Y-m-d', '2018-11-1'));
    }

    public function calculateInitial()
    {
        $balanceAt = $this->balance->getBalanceAt();
        $days = $balanceAt->diff($this->startDate);
        //If last balance is before the start date, calculate the initial balance
        if (!$days->invert) {
            $this->initial -= $this->dailyAverage * $days->days;
            $this->initial -= $this->balance->getAmount();

            foreach ($this->recurringTransactions as $txn) {
                $recurrences = $balanceAt->diff($this->startDate)->m + $balanceAt->diff($this->startDate)->y * 12;
                $this->initial -= $recurrences * $txn->getAmount();
            }
        }

        $this->total = $this->initial;
    }

    public function enumerateSheet()
    {
        //TODO: add transactions in the mix

        $dateRange = new \DatePeriod($this->startDate, new \DateInterval('P1D'), $this->endDate);

        $index = 0;
        foreach ($dateRange as $currentDate) {

            //Check if balance is within the sheet
            $balanceDate = $this->balance->getBalanceAt();
            $balanceDateDiff = $balanceDate->diff($currentDate);
            if (0 == $balanceDateDiff->days && !$balanceDateDiff->invert) {
                $this->total = $this->balance->getAmount();
                $this->addSheetRow("Balance", $balanceDate, $balanceDateDiff, $this->balance->getAmount(), $this->total, $index++);
            }

            //Daily additions
            $this->total -= $this->dailyAverage;
            $this->addSheetRow("Daily", $currentDate, $this->today->diff($currentDate), $this->dailyAverage, $this->total, $index++);


            //Recurring Transactions additions
            foreach ($this->recurringTransactions as $txn) {

                $txnDate = $txn->getDate();
                $txnDate->setDate($this->startDate->format('Y'), $this->startDate->format('m'), $txnDate->format('d'));

                $dateDiff = $txnDate->diff($currentDate);
                if (0 == $dateDiff->d) {
                    $this->total -= $txn->getAmount();
                    $this->addSheetRow($txn->getTitle(), $txnDate, $this->today->diff($currentDate), $txn->getAmount(), $this->total, $index++);
                }
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     *
     * @return \DateTime
     */
    public function getToday()
    {
        return $this->today;
    }

    /**
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @return Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     *
     * @return \DatePeriod
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }

    /**
     *
     * @return array
     */
    public function getRecurringTransactions()
    {
        return $this->recurringTransactions;
    }

    /**
     *
     * @return int
     */
    public function getInitial()
    {
        return $this->initial;
    }

    /**
     *
     * @return int
     */
    public function getDailyAverage()
    {
        return $this->dailyAverage;
    }

    /**
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     *
     * @param array $sheet
     */
    public function setSheet($sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     *
     * @param \DateTime $today
     */
    public function setToday(\DateTime $today)
    {
        $this->today = $today;
    }

    /**
     *
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     *
     * @param Balance $balance
     */
    public function setBalance(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     *
     * @param \DatePeriod $dateRange
     */
    public function setDateRange(\DatePeriod $dateRange)
    {
        $this->dateRange = $dateRange;
    }

    /**
     *
     * @param array $recurringTransactions
     */
    public function setRecurringTransactions($recurringTransactions)
    {
        $this->recurringTransactions = $recurringTransactions;
    }

    /**
     *
     * @param int $initial
     */
    public function setInitial($initial)
    {
        $this->initial = $initial;
    }

    /**
     *
     * @param int $dailyAverage
     */
    public function setDailyAverage($dailyAverage)
    {
        $this->dailyAverage = $dailyAverage;
    }

    /**
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function addSheetRow($title, $date, $days, $amount, $balance, $index = false)
    {
        if (false === $index) {
            $this->sheet[] = new Sheet($title, $date, $days, $amount, $balance);
        } else {
            $this->sheet[$index] = new Sheet($title, $date, $days, $amount, $balance);
        }
    }

    public function addRecurringTransaction($title, $amount, $date)
    {
        $this->recurringTransactions[] = new RecurringTransactions($title, $amount, $date);
    }

}
