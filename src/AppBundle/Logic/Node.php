<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

/**
 * Description of Node
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Node
{

  /**
   *
   * @var string
   */
  protected $name;

  /**
   *
   * @var \DateInterval
   */
  protected $duration;

  /**
   *
   * @var bool defines if a dot is movable
   */
  protected $strict = false;

  /**
   *
   * @var bool marks a dot as busy
   */
  protected $busy = true;

  /**
   *
   * @var int timestamp
   */
  protected $start;

  function __construct($name, $duration, $strict = false, $busy = true, $start = null)
  {
    $this->setName($name);
    $this->setDuration($duration);
    $this->setStrict($strict);
    $this->setStart($start);
    $this->setBusy($busy);
    if ($this->isStrict() && (!$this->validStart())) {
      throw new \Exception("Strict nodes require Start");
    }
  }

  /**
   * 
   * @return string
   */
  function getName()
  {
    return $this->name;
  }

  /**
   * 
   * @return \DateInterval
   */
  function getDuration()
  {
    return $this->duration;
  }

  /**
   * 
   * @return bool
   */
  function isStrict()
  {
    return $this->strict;
  }

  /**
   * 
   * @return bool
   */
  function isBusy()
  {
    return $this->busy;
  }

  /**
   * 
   * @param string $name
   */
  function setName($name)
  {
    $this->name = $name;
  }

  /**
   * 
   * @param int $duration in minutes
   */
  function setDuration($duration)
  {
    $this->duration = \DateInterval::createFromDateString("$duration minutes");
  }

  /**
   * 
   * @param bool $strict
   */
  function setStrict($strict)
  {
    $this->strict = $strict;
  }

  /**
   * 
   * @param bool $busy
   */
  function setBusy($busy)
  {
    $this->busy = $busy;
  }

  /**
   * 
   * @return int timestamp
   */
  function getStart()
  {
    return $this->start;
  }

  /**
   * 
   * @param int $start timestamp
   */
  function setStart($start)
  {
    $this->start = $start;
  }

  function validStart()
  {
    if (is_null($this->getStart()) ||
        is_nan($this->getStart()) ||
        (false === strtotime($this->getStart()))) {
      return false;
    }
    return true;
  }

}
