<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

use AppBundle\Logic\Dot;

//use AppBundle\Entity\Tasks;
//use AppBundle\Entity\Days;

/**
 * Description of Roadmap
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Roadmap
{

  /**
   *
   * @var array
   */
  protected $tasks;

  /**
   *
   * @var array
   */
  protected $days;

  /**
   *
   * @var array
   */
  protected $dots;

  public function __construct()
  {
    $this->dots = array();
  }

  /**
   * 
   * @return array
   */
  public function getTasks()
  {
    return $this->tasks;
  }

  /**
   * 
   * @return array
   */
  public function getDays()
  {
    return $this->days;
  }

  /**
   * 
   * @return array
   */
  public function getDots()
  {
    return $this->dots;
  }

  /**
   * 
   * @param array $dots
   */
  public function setDots($dots)
  {
    $this->dots = $dots;
  }

  /**
   * 
   * @param array $tasks
   */
  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }

  /**
   * 
   * @param array $days
   */
  public function setDays($days)
  {
    $this->days = $days;
  }

  public function addDot($dot)
  {
    $this->dots[$dot->getTimestamp()->format('D, M d')][]=$dot;
  }

  public function populateDots()
  {
    //lets produce dots for a 1 day
    $timezone = new \DateTimeZone("Africa/Cairo");
    $end = new \DateTime("NOW", $timezone);
    $cur = new \DateTime("NOW", $timezone);
    $today = new \DateTime("NOW", $timezone);
    $end->setTime(20, 00);
    $index = 0;
    while ($cur < $end) {
      $timestamp = new \DateTime();
      $timestamp->setTimestamp($cur->getTimestamp());
      $this->addDot(new Dot($this->tasks[$index]->getTask(), $timestamp, $this->tasks[$index]->getEst(), $this->tasks[$index]->getUrgency(), $this->tasks[$index]->getPriority(), 'task'));

      if ($this->tasks[$index]->getEst())
        $cur->add(\DateInterval::createFromDateString($this->tasks[$index]->getEst() . " minutes"));
      $index++;
    }
  }

}
