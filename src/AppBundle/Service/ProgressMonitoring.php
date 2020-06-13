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
  private $tasksCompletedCount;

  /**
   *
   * @var float tasks this month increase
   */
  private $tasksCompletedProgress;

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
    $this->setTasksCompletedCount();
    $this->setTasksCompletedProgress();
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
    $date->modify("-1 year");
    $clientsLastYear = $this->em->getRepository('AppBundle:Client')
        ->getCreatedTillYear($date->format('Y'));
    $this->clientsProgress = (($this->clientsCount - $clientsLastYear) / $clientsLastYear) * 100;
  }

  function getClientsProgress()
  {
    return number_format($this->clientsProgress, 2);
  }

  public function setTasksCompletedCount()
  {
    $date = \AppBundle\Util\DateRanges::getMonthStart();
    $this->tasksCompletedCount = $this->em->getRepository('AppBundle:Tasks')
        ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'));
  }

  function getTasksCompletedCount()
  {
    return $this->tasksCompletedCount;
  }

  public function setTasksCompletedProgress()
  {
    $date = \AppBundle\Util\DateRanges::getMonthStart();
    $tasksLastMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'), $date->format('d'));
    $divisionByZero = $tasksLastMonth ? $tasksLastMonth : 1;

    $this->tasksCompletedProgress = (($this->tasksCompletedCount - $tasksLastMonth) / $divisionByZero) * 100;
  }

  function getTasksCompletedProgress()
  {
    return number_format($this->tasksCompletedProgress, 2);
  }

  public function setRevenueSum()
  {
    $date = new \DateTime();
    $this->revenueSum = $this->em->getRepository('AppBundle:AccountTransactions')
        ->getRevenueSumByMonth($date->format('Y'), $date->format('m'));
  }

  function getRevenueSum()
  {
    return number_format($this->revenueSum);
  }

  function setRevenueProgress()
  {
    $date = \AppBundle\Util\DateRanges::getMonthStart();
    $revenueLastMonth = $this->em->getRepository('AppBundle:AccountTransactions')
        ->getRevenueSumByMonth($date->format('Y'), $date->format('m'), $date->format('d'));

    $divisionByZero = $revenueLastMonth ? $revenueLastMonth : 1;
    $this->revenueProgress = (($this->revenueSum - $revenueLastMonth) / $divisionByZero) * 100;
  }

  function getRevenueProgress()
  {
    return number_format($this->revenueProgress, 2);
  }

  function setDurationSum()
  {
    $date = new \DateTime();
    $this->durationSum = $this->em->getRepository('AppBundle:Tasks')
        ->getSumDurationByMonth($date->format('Y'), $date->format('m'));
  }

  function getDurationSum()
  {
    return number_format($this->durationSum / 60) . ':' . ($this->durationSum % 60);
  }

  function setDurationProgress()
  {
    $date = \AppBundle\Util\DateRanges::getMonthStart();
    $durationLastMonth = $this->em->getRepository('AppBundle:Tasks')
        ->getSumDurationByMonth($date->format('Y'), $date->format('m'), $date->format('d'));
    $divisionByZero = $durationLastMonth ? $durationLastMonth : 1;
    $this->durationProgress = ((($this->durationSum - $durationLastMonth) / $divisionByZero) * 100);
  }

  function getDurationProgress()
  {
    return number_format($this->durationProgress, 2);
  }

}
