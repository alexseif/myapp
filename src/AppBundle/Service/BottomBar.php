<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

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

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
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
   * @return array Tasks[]
   */
  public function getContracts()
  {
    $contracts = $this->em->getRepository('AppBundle:Contract')->findAll();
    foreach ($contracts as $contract) {
      $tasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedToday();
      $duration = 0;
      foreach ($tasks as $task) {
        if ($contract->getClient() == $task->getClient()) {
          $duration += $task->getDuration();
        }
      }
      $contract->percentage = $duration / ($contract->getHoursPerDay() * 60) * 100;
    }
    return $contracts;
  }

}
