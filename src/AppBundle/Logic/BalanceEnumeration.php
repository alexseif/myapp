<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

use AppBundle\Entity\Balance;
use AppBundle\Model\RecurringTransactions;

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
   * @var integer initial
   */
  private $initial = 0;

  /**
   *
   * @var integer dailyAverage
   */
  private $dailyAverage = 200;

  /**
   *
   * @var integer total
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
    $this->startDate->setDate($this->today->format('Y'), $this->today->format('n'), 1);
    $this->endDate->setDate($this->today->format('Y'), $this->today->format('n'), 1);
    // End of month
    $this->endDate->add(new \DateInterval('P1M'));

    $this->enumerateRecurringTransactions();
    $this->calculateInitial();
    $this->calculateRecurrences();
    $this->enumerateSheet();
  }

  public function enumerateRecurringTransactions()
  {
    $this->recurringTransactions[] = new RecurringTransactions('Mediatemple', '400', \DateTime::createFromFormat('Y-m-d', '2018-11-18'));
    $this->recurringTransactions[] = new RecurringTransactions('Vodafone', '250', \DateTime::createFromFormat('Y-m-d', '2018-11-1'));
    $this->recurringTransactions[] = new RecurringTransactions('EKA', '-2000', \DateTime::createFromFormat('Y-m-d', '2018-11-1'));
  }

  public function calculateInitial()
  {
    $balanceAt = $this->balance->getBalanceAt();
    $days = $balanceAt->diff($this->startDate);
    $this->initial -= $this->dailyAverage * $days->days;
    $this->initial -= $this->balance->getAmount();

    $this->total = $this->initial;
  }

  public function calculateRecurrences()
  {
    $balanceAt = $this->balance->getBalanceAt();

    foreach ($this->recurringTransactions as $txn) {
      $recurrences = $balanceAt->diff($this->startDate)->m + $balanceAt->diff($this->startDate)->y * 12;
      $this->initial -= $recurrences * $txn->getAmount();
    }

    $this->total = $this->initial;
  }

  public function enumerateSheet()
  {
    $interval = new \DateInterval('P1D');
    $dateRange = new \DatePeriod($this->startDate, $interval, $this->endDate);

    $index = 0;
    foreach ($dateRange as $dateIndex) {

      $dateDiff = $this->today->diff($dateIndex);
      $this->sheet[$index]['item'] = "Daily";
      $this->sheet[$index]['dateIndex'] = $dateIndex;
      $this->sheet[$index]['days'] = $dateDiff;
      $this->sheet[$index]['value'] = $this->dailyAverage;
      $this->total -= $this->sheet[$index]['value'];
      $this->sheet[$index]['balance'] = $this->total;
      $index++;

      foreach ($this->recurringTransactions as $txn) {

        $txnDate = $txn->getDate();
        $txnDate->setDate($this->startDate->format('Y'), $this->startDate->format('m'), $txnDate->format('d'));

        $dateDiff = $txnDate->diff($dateIndex);
        if (0 == $dateDiff->d) {

          $this->sheet[$index]['item'] = $txn->getTitle();
          $this->sheet[$index]['dateIndex'] = $txnDate;
          $this->sheet[$index]['days'] = $this->today->diff($dateIndex);
          $this->sheet[$index]['value'] = $txn->getAmount();
          $this->total -= $this->sheet[$index]['value'];
          $this->sheet[$index]['balance'] = $this->total;

          $index++;
        }
      }
    }
  }

  function getSheet()
  {
    return $this->sheet;
  }

  function getToday()
  {
    return $this->today;
  }

  function getStartDate()
  {
    return $this->startDate;
  }

  function getEndDate()
  {
    return $this->endDate;
  }

  function getBalance()
  {
    return $this->balance;
  }

  function getDateRange()
  {
    return $this->dateRange;
  }

  function getRecurringTransactions()
  {
    return $this->recurringTransactions;
  }

  function getInitial()
  {
    return $this->initial;
  }

  function getDailyAverage()
  {
    return $this->dailyAverage;
  }

  function getTotal()
  {
    return $this->total;
  }

  function setSheet($sheet)
  {
    $this->sheet = $sheet;
  }

  function setToday(\DateTime $today)
  {
    $this->today = $today;
  }

  function setStartDate(\DateTime $startDate)
  {
    $this->startDate = $startDate;
  }

  function setEndDate(\DateTime $endDate)
  {
    $this->endDate = $endDate;
  }

  function setBalance(Balance $balance)
  {
    $this->balance = $balance;
  }

  function setDateRange(\DatePeriod $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  function setRecurringTransactions($recurringTransactions)
  {
    $this->recurringTransactions = $recurringTransactions;
  }

  function setInitial($initial)
  {
    $this->initial = $initial;
  }

  function setDailyAverage($dailyAverage)
  {
    $this->dailyAverage = $dailyAverage;
  }

  function setTotal($total)
  {
    $this->total = $total;
  }

}
