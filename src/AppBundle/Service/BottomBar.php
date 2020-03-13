<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\CostService;

/**
 * Description of Bottom Bar Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class BottomBar
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

  public function __construct(EntityManager $em, CostService $cs)
  {
    $this->em = $em;
    $this->cs = $cs;
  }

  /**
   * 
   * @return array Thing[]
   */
  public function getThings()
  {
    $things = $this->em->getRepository('AppBundle:Thing')->findBy([], ['createdAt' => 'asc'], 5);
    return $things;
  }

  /**
   * 
   * @return array Tasks[]
   */
  public function getTasks()
  {
    $tasks = $this->em->getRepository('AppBundle:Tasks')->focusList(5);
    return $tasks;
  }

  /**
   * 
   * @return array Contract[]
   */
  public function getContracts()
  {
    $contracts = $this->em->getRepository('AppBundle:Contract')->findAll();
    foreach ($contracts as $contract) {
      $completedByClientThisMonth = $this->em->getRepository('AppBundle:Tasks')->findDurationCompletedByClientThisMonth($contract->getClient())['duration'];
      $monthStart = new \DateTime();
      $monthStart->setDate($monthStart->format('Y'), $monthStart->format('m'), 1);
      $monthStart->setTime(00, 00, 00);
      if ($contract->getStartedAt() > $monthStart) {
        $monthStart->setDate($contract->getStartedAt()->format('Y'), $contract->getStartedAt()->format('m'), $contract->getStartedAt()->format('d'));
      }
      $monthEnd = new \DateTime();
      $monthEnd->setDate($monthEnd->format('Y'), $monthEnd->format('m'), $monthEnd->format('t'));
      $monthEnd->setTime(23, 59, 59);
      $workingDaysSoFar = \AppBundle\Util\DateRanges::numberOfWorkingDays($monthStart, new \DateTime());
      $workingDaysLeft = \AppBundle\Util\DateRanges::numberOfWorkingDays(new \DateTime(), $monthEnd);
      $expctedByClientThisMonth = $contract->getHoursPerDay() * $workingDaysSoFar * 60;
      $duration = $this->em->getRepository('AppBundle:Tasks')->findCompletedByClientToday($contract->getClient())['duration'];
      $difference = $expctedByClientThisMonth - $completedByClientThisMonth;
      $workingDaysLeft = ($workingDaysLeft) ?: 1;
      $overMonth = $difference / $workingDaysLeft;
      $minutesPerDay = ($contract->getHoursPerDay() * 60) + $overMonth;
      $contract->percentage = $duration / $minutesPerDay * 100;
      $contract->setName($contract->getName() . " " . floor($minutesPerDay / 60) . ":" . ($minutesPerDay % 60) . " || " . floor($difference / 60) . ":" . ($difference % 60));
    }
    return $contracts;
  }

  public function getProgress()
  {
    $earnedLogic = new \AppBundle\Logic\EarnedLogic($this->em, $this->cs);
    $earned = $earnedLogic->getEarned();
    return [
      'earned' => $earned,
      'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
      'costOfLife' => $this->cs,
    ];
  }

  public function getObjectives()
  {
    $objectives = $this->em->getRepository('AppBundle:Objective')->findBy([], ['createdAt' => 'asc'], 5);
    return $objectives;
  }

}
