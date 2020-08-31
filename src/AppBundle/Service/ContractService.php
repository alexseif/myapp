<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Description of Contract Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ContractService
{

  /**
   * 
   * @var EntityManager $em
   */
  protected $em;

  /**
   * 
   * @var CostService $cs
   */
  protected $cs;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  /**
   * 
   * @return array progress of contracts
   */
  public function progress()
  {
    $contracts = $this->em->getRepository('AppBundle:Contract')->findAll();
    foreach ($contracts as $contract) {
      $monthTotal = 0;
      $expectedSoFar = 0;
      $monthRemaining = 0;

      $monthStart = \AppBundle\Util\DateRanges::getMonthStart();
      $monthEnd = new \DateTime();
      if ($contract->getStartedAt() > $monthStart) {
        $monthStart->setDate($contract->getStartedAt()->format('Y'), $contract->getStartedAt()->format('m'), $contract->getStartedAt()->format('d'));
      }
      $monthTotal += $this->em->getRepository('AppBundle:Tasks')->findDurationCompletedByClientByRange($contract->getClient(), $monthStart, $monthEnd)['duration'];
      $monthHolidays = $this->em->getRepository('AppBundle:Holiday')->findByRange($monthStart, $monthEnd);
      $workweek = [1, 2, 3, 4, 7];
      foreach ($monthHolidays as $holiday) {
        if (in_array($holiday->getDate()->format('N'), $workweek)) {
          if ('Personal' == $holiday->getType()) {
            if (30 == $contract->getClient()->getId()) {
              $monthTotal += 240;
            }
          } else {
            $monthTotal += 240;
          }
        }
      }
      $workingDaysSoFar = \AppBundle\Util\DateRanges::numberOfWorkingDays($monthStart, new \DateTime());
      $expectedSoFar += $contract->getHoursPerDay() * $workingDaysSoFar * 60;
      $workingDaysLeft = \AppBundle\Util\DateRanges::numberOfWorkingDays(new \DateTime(),  \AppBundle\Util\DateRanges::getMonthEnd());
      $todayTotal = $this->em->getRepository('AppBundle:Tasks')->findCompletedByClientToday($contract->getClient())['duration'];
      $monthRemaining = $expectedSoFar - $monthTotal;
      $workingDaysLeft = ($workingDaysLeft) ?: 1;
      $overMonth = $monthRemaining / $workingDaysLeft;
      $overdueTotal = ($contract->getHoursPerDay() * 60) + $overMonth;
      $contract->percentage = $todayTotal / $overdueTotal * 100;

      $contract->setName($contract->getName() . " " . floor($overdueTotal / 60) . ":" . ($overdueTotal % 60) . " || " . floor($monthRemaining / 60) . ":" . ($monthRemaining % 60));
    }
    return $contracts;
  }

}
