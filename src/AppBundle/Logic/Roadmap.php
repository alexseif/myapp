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
   * @var \DateTimeZone
   */
  protected $timezone;

  /**
   *
   * @var \DateTime
   */
  protected $start;

  /**
   *
   * @var \DateTime 
   */
  protected $current;

  /**
   *
   * @var \DateTime
   */
  protected $end;

  /**
   *
   * @var array
   */
  protected $dots;

  public function __construct()
  {
    $this->setDots(array());
    $this->setTimezone(new \DateTimeZone("Africa/Cairo"));
    $this->setStart(new \DateTime());
    $this->getStart()->setTimezone($this->getTimezone());
//    $this->getStart()->setDate(2017, 3, 10);
//    $this->getStart()->setTime(8, 00);
    $this->setEnd(new \DateTime());
    $this->getEnd()->setTimezone($this->getTimezone());
    $this->getEnd()->add(\DateInterval::createFromDateString("5 days"));
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

  /**
   * 
   * @return \DateTime
   */
  public function getStart()
  {
    return $this->start;
  }

  /**
   * 
   * @return \DateTime
   */
  public function getEnd()
  {
    return $this->end;
  }

  /**
   * 
   * @param \DateTime $start
   */
  public function setStart(\DateTime $start)
  {
    $this->start = $start;
  }

  /**
   * 
   * @param \DateTime $end
   */
  public function setEnd(\DateTime $end)
  {
    $this->end = $end;
  }

  /**
   * 
   * @return \DateTimeZone
   */
  public function getTimezone()
  {
    return $this->timezone;
  }

  /**
   * 
   * @param \DateTimeZone $timezone
   */
  public function setTimezone(\DateTimeZone $timezone)
  {
    $this->timezone = $timezone;
  }

  /**
   * 
   * @return \DateTime
   */
  public function getCurrent()
  {
    return $this->current;
  }

  /**
   * 
   * @param \DateTime $current
   */
  public function setCurrent(\DateTime $current)
  {
    $this->current = $current;
  }

  public function checkDateTimeInDot($dot, $start, $end)
  {
    if (($start >= $dot->getStart() && $start < $dot->getEnd()) ||
        ($end >= $dot->getStart() && $end <= $dot->getEnd())) {
      return true;
    } else {
      return false;
    }
  }

  public function checkEndInDot($dot, $end)
  {
    if ($end >= $dot->getStart() && $end <= $dot->getEnd()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkEnd($end)
  {
    foreach ($this->getDots()as $dot) {
      if ($this->checkEndInDot($dot, $end)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function findAvailableTime($proposedDot)
  {
    $start = clone $this->current;
    $end = clone $start;
    $end->add($proposedDot->getDuration());
    foreach ($this->getDots() as $dot) {
      if ($start >= $dot->getEnd()) {
        continue;
      }
      if ($this->checkDateTimeInDot($dot, $start, $end)) {
        $start = $dot->getEnd();
        $end = clone $start;
        $end->add($proposedDot->getDuration());
      } else {
        $reverseCheck = false;
        $proposedDot->setStart($start);
        $proposedDot->setEnd($end);
        foreach ($this->getDots() as $dotCheck) {
          if ($start >= $dot->getEnd()) {
            continue;
          }
          if ($this->checkDateTimeInDot($proposedDot, $dotCheck->getStart(), $dotCheck->getEnd())) {
            $reverseCheck = true;
            break;
          }
        }
        if ($reverseCheck)
          continue;
        return $start;
      }
    }
    return $start;
  }

  /**
   * 
   * @param Dot $dot
   */
  public function addDot($dot)
  {
    if (is_null($dot->getEnd())) {
      $dot->calculateEnd();
    }
    $this->dots[] = $dot;
  }

  public function populateDots()
  {
    $this->routineSetup();
    $this->setCurrent(clone $this->getStart());
    $index = 0;
    while (($this->getCurrent() < $this->getEnd()) && ($index < count($this->tasks))) {
      if ($this->tasks[$index]->getEst()) {
        $dot = new Dot($this->tasks[$index]->getTask(), 'Task', \DateInterval::createFromDateString($this->tasks[$index]->getEst() . " minutes"));
        $dot->setUrgency($this->tasks[$index]->getUrgency());
        $dot->setPriority($this->tasks[$index]->getPriority());
        $start = new \DateTime();
        $start = $this->findAvailableTime($dot);
        $dot->setStart(clone $start);
        $dot->calculateEnd();
        $this->current = clone $dot->getEnd();
        $this->addDot($dot);
        $this->sortDots();
      }
      $index++;
    }
  }

  //FIXME: buggy if you're between 0:00 and 7:00 sleep from previous day doesn't appear
  //TODO: maybe add working hours and make it easier to assign tasks
  public function routineSetup()
  {
    $interval = new \DateInterval('P1D');
    $daterange = new \DatePeriod($this->start, $interval, $this->end);
    $type = 'Routine';
    $eatDescription = "Don't eat infornt of the computer / On your desk / where you work";
    foreach ($daterange as $date) {
      $time = clone $date;
      $time->setTimezone($this->getTimezone());
//      $time->setDate(2017, 3, 10);

      $dot = new Dot('Breakfast', $type, \DateInterval::createFromDateString("20 minutes"));
      $dot->setStart(clone $time->setTime(7, 00));
      $dot->setDescription($eatDescription);
      $this->addDot($dot);

      $dot = new Dot('Read', $type, \DateInterval::createFromDateString("40 minutes"));
      $dot->setStart(clone $time->setTime(7, 20));
      $this->addDot($dot);

      $dot = new Dot('Break/Snack', $type, \DateInterval::createFromDateString("20 minutes"));
      $dot->setStart(clone $time->setTime(10, 00));
      $dot->setDescription($eatDescription);
      $this->addDot($dot);

      $dot = new Dot('Lunch', $type, \DateInterval::createFromDateString("20 minutes"));
      $dot->setStart(clone $time->setTime(13, 00));
      $this->addDot($dot);

      $dot = new Dot('Break/Snack', $type, \DateInterval::createFromDateString("20 minutes"));
      $dot->setStart(clone $time->setTime(16, 00));
      $dot->setDescription($eatDescription);
      $this->addDot($dot);

      $dot = new Dot('Dinner', $type, \DateInterval::createFromDateString("20 minutes"));
      $dot->setStart(clone $time->setTime(19, 00));
      $dot->setDescription($eatDescription);
      $this->addDot($dot);

      $dot = new Dot('Sleep', $type, \DateInterval::createFromDateString("8 hours"));
      $dot->setStart(clone $time->setTime(23, 00));
      $this->addDot($dot);
    }
  }

  public function sortDots()
  {
    $ord = array();
    foreach ($this->dots as $key => $value) {
      $ord[] = $value->getStart();
    }
    array_multisort($ord, SORT_ASC, $this->dots);
  }

}
