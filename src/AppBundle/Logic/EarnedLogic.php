<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

/**
 * Description of CostOfLifeLogic
 *
 * @author Alex Seif <me@alexseif.com>
 */
class EarnedLogic
{

  protected $em;
  protected $costOfLife;
  protected $issuedThisMonth, $monthly, $weekly, $daily = 0;

  public function __construct($em, $costOfLife)
  {
    $this->em = $em;
    $this->costOfLife = $costOfLife;
    $this->calculateDaily();
    $this->calculateWeekly();
    $this->calculateMonthly();
  }

  public function getEarned()
  {
    return ['daily' => $this->getDaily(),
      'weekly' => $this->getWeekly(),
      'monthly' => $this->getMonthly(),
    ];
  }

  public function calculateMonthly()
  {
    $issuedThisMonth = $this->em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }
    $this->setIssuedThisMonth($issued);
    $this->setMonthly(($this->em->getRepository('AppBundle:Tasks')->sumCompletedEstThisMonth()['est'] / 60) * $this->costOfLife->getHourly());
  }

  public function calculateWeekly()
  {
    $this->setWeekly(($this->em->getRepository('AppBundle:Tasks')->sumCompletedEstThisWeek()['est'] / 60) * $this->costOfLife->getHourly());
  }

  public function calculateDaily()
  {
    $this->setDaily(($this->em->getRepository('AppBundle:Tasks')->sumCompletedEstToday()['est'] / 60) * $this->costOfLife->getHourly());
  }

  function getMonthly()
  {
    return $this->monthly;
  }

  function getWeekly()
  {
    return $this->weekly;
  }

  function getDaily()
  {
    return $this->daily;
  }

  function setMonthly($monthly)
  {
    $this->monthly = $monthly;
  }

  function setWeekly($weekly)
  {
    $this->weekly = $weekly;
  }

  function setDaily($daily)
  {
    $this->daily = $daily;
  }

  function getIssuedThisMonth()
  {
    return $this->issuedThisMonth;
  }

  function setIssuedThisMonth($issuedThisMonth)
  {
    $this->issuedThisMonth = $issuedThisMonth;
  }

}
