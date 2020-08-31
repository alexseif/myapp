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
      $monthStart = \AppBundle\Util\DateRanges::getMonthStart();
      $monthEnd = \AppBundle\Util\DateRanges::getMonthEnd();
      if ($contract->getStartedAt() > $monthStart) {
        $monthStart->setDate($contract->getStartedAt()->format('Y'), $contract->getStartedAt()->format('m'), $contract->getStartedAt()->format('d'));
      }
      $completedByClientThisMonth = $this->em->getRepository('AppBundle:Tasks')->findDurationCompletedByClientByRange($contract->getClient(), $monthStart, $monthEnd)['duration'];
      $workingDaysSoFar = \AppBundle\Util\DateRanges::numberOfWorkingDays($monthStart, new \DateTime());
      $workingDaysLeft = \AppBundle\Util\DateRanges::numberOfWorkingDays(new \DateTime(), $monthEnd);
      $expctedByClientThisMonth = $contract->getHoursPerDay() * $workingDaysSoFar * 60;
      $duration = $this->em->getRepository('AppBundle:Tasks')->findCompletedByClientToday($contract->getClient())['duration'];
      $difference = $expctedByClientThisMonth - $completedByClientThisMonth;
      $workingDaysLeft = ($workingDaysLeft) ?: 1;
      $overMonth = $difference / $workingDaysLeft;
      $minutesPerDay = ($contract->getHoursPerDay() * 60) + $overMonth;
      if ($difference < 0) {
        $minutesPerDay = ($contract->getHoursPerDay() * 60);
      }
      $contract->percentage = $duration / $minutesPerDay * 100;
      $contract->setName($contract->getName() . " " . floor($minutesPerDay / 60) . ":" . ($minutesPerDay % 60) . " || " . floor($difference / 60) . ":" . ($difference % 60));
    }
    return $contracts;
  }

}
