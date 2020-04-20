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
class ProgressMonitoring
{

  /**
   * 
   * @var EntityManager $em
   */
  protected $em;

  /**
   *
   * @var int total number of clients
   */
  private $clientsCount;

  /**
   *
   * @var float clients annual increase
   */
  private $clientsProgress;

  /**
   *
   * @var int total number of tasks this month
   */
  private $tasksCount;

  /**
   *
   * @var float tasks this month increase
   */
  private $tasksProgress;

  /**
   *
   * @var float revenue sum this month
   */
  private $revenueSum;

  /**
   *
   * @var float revenue this month increase
   */
  private $revenueProgress;

  /**
   *
   * @var int duration sum this month
   */
  private $durationSum;

  /**
   *
   * @var int duration this month increase
   */
  private $durationProgress;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
    $this->setClientsCount();
    $this->setClientsProgress();
    $this->setTasksCount();
    $this->setTasksProgress();
    $this->setRevenueSum();
    $this->setRevenueProgress();
    $this->setDurationSum();
    $this->setDurationProgress();
  }

  public function setClientsCount()
  {
    $this->clientsCount = count($this->em->getRepository('AppBundle:Client')->findBy([
          "enabled" => true]));
  }

  public function getClientsCount()
  {
    return $this->clientsCount;
  }

  public function setClientsProgress()
  {
    $date = new \DateTime();
    $clientsThisYear = $this->em->getRepository('AppBundle:Client')
        ->getCreatedInYear($date->format('Y'));
    $date->modify("-1 month");
    $clientsLastYear = $this->em->getRepository('AppBundle:Client')
        ->getCreatedInYear($date->format('Y'));
    $this->clientsProgress = number_format(((($clientsThisYear - $clientsLastYear) / $this->getClientsCount() ) * 100), 2);
  }

  function getClientsProgress()
  {
    return $this->clientsProgress;
  }

  public function setTasksCount()
  {
    $date = new \DateTime();
    $this->tasksCount = $this->em->getRepository('AppBundle:Tasks')
        ->getCreatedInMonth($date->format('Y'), $date->format('m'));
  }

  function getTasksCount()
  {
    return $this->tasksCount;
  }

  public function setTasksProgress()
  {
    $date = new \DateTime();
    $tasksThisMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getCreatedInMonth($date->format('Y'), $date->format('m'));
    $date->modify("-1 month");
    $tasksLastMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getCreatedInMonth($date->format('Y'), $date->format('m'));
    $divisionByZero = $tasksLastMonth ? $tasksLastMonth : 1;

    $this->tasksProgress = number_format((($tasksThisMonth - $tasksLastMonth) / $divisionByZero) * 100, 2);
  }

  function getTasksProgress()
  {
    return $this->tasksProgress;
  }

  public function setRevenueSum()
  {
    $date = new \DateTime();
    $this->revenueSum = number_format($this->em->getRepository('AppBundle:AccountTransactions')
            ->getRevenueSumByMonth($date->format('Y'), $date->format('m')));
  }

  function getRevenueSum()
  {
    return $this->revenueSum;
  }

  function setRevenueProgress()
  {
    $date = new \DateTime();
    $revenueThisMonth = $this->em->getRepository('AppBundle:AccountTransactions')
        ->getRevenueSumByMonth($date->format('Y'), $date->format('m'));
    $date->modify("-1 month");
    $revenueLastMonth = $this->em->getRepository('AppBundle:AccountTransactions')
        ->getRevenueSumByMonth($date->format('Y'), $date->format('m'));
    $divisionByZero = $revenueLastMonth ? $revenueLastMonth : 1;
    $this->revenueProgress = number_format(((($revenueThisMonth - $revenueLastMonth) / $divisionByZero) * 100), 2);
  }

  function getRevenueProgress()
  {
    return $this->revenueProgress;
  }

  function setDurationSum()
  {
    $date = new \DateTime();
    $this->durationSum = $this->em->getRepository('AppBundle:Tasks')
        ->getSumDurationInMonth($date->format('Y'), $date->format('m'));
  }

  function getDurationSum()
  {
    return number_format($this->durationSum / 60) . ':' . ($this->durationSum % 60);
  }

  function setDurationProgress()
  {
    $date = new \DateTime();
    $durationThisMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getSumDurationInMonth($date->format('Y'), $date->format('m'));
    $date->modify("-1 month");
    $durationLastMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getSumDurationInMonth($date->format('Y'), $date->format('m'));
    $divisionByZero = $durationLastMonth ? $durationLastMonth : 1;
    $this->durationProgress = number_format(((($durationThisMonth - $durationLastMonth) / $divisionByZero) * 100), 2);
  }

  function getDurationProgress()
  {
    return $this->durationProgress;
  }

}
