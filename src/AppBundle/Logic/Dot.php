<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

/**
 * Description of Dot
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Dot
{

  /**
   *
   * @var string
   */
  protected $name;

  /**
   *
   * @var string
   */
  protected $type;

  /**
   *
   * @var \DateTime
   */
  protected $start;

  /**
   *
   * @var \DateTime
   */
  protected $end;

  /**
   *
   * @var \DateInterval
   */
  protected $duration;

  /**
   *
   * @var string
   */
  protected $description;

  /**
   *
   * @var int
   */
  protected $urgency = 0;

  /**
   *
   * @var int
   */
  protected $priority = 0;

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
   * @param string $name
   * @param string $type
   * @param int $duration
   * @param \DateTime $start [optional]
   * @param string $description [optional]
   * @param int $urgency [optional]
   * @param int $priority [optional]
   * @param bool $strict [optional]
   * @param bool $busy [optional]
   */
  function __construct($name, $type, $duration, \DateTime $start = null, $description = null, $urgency = 0, $priority = 0, $strict = false, $busy = true)
  {
    $this->setName($name);
    $this->setType($type);
    $this->setStart($start);
    $this->setDuration($duration);
    $this->setDescription($description);
    $this->setUrgency($urgency);
    $this->setPriority($priority);
    $this->setStrict($strict);
    $this->setBusy($busy);
  }

  /**
   * 
   * @param string $title
   * @param string $type
   * @param int $duration the number of minutes
    public function __construct($title, $type, $duration)
    {
    $this->setName($title);
    $this->setDuration($duration);
    $this->setType($type);
    }
   */

  /**
   * 
   * @return string
   */
  public function getName()
  {
    return $this->name;
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
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * 
   * @return int
   */
  public function getUrgency()
  {
    return $this->urgency;
  }

  /**
   * 
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * 
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * 
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * 
   * @param \DateTime $start
   */
  public function setStart(\DateTime $start =null)
  {
    $this->start = $start;
  }

  /**
   * 
   * @param \DateTime $end
   */
  public function setEnd(\DateTime $end)
  {
    //TODO: needs to be greater than $start
    if ($end < $this->getStart()) {
      throw new \Exception("End cannot be before Start");
    }
    $this->end = $end;
  }

  /**
   * 
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * 
   * @param int $urgency
   */
  public function setUrgency($urgency)
  {
    $this->urgency = $urgency;
  }

  /**
   * 
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }

  /**
   * 
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * 
   * @return \DateInterval
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * 
   * @return bool
   */
  function getStrict()
  {
    return $this->strict;
  }

  /**
   * 
   * @return bool
   */
  function getBusy()
  {
    return $this->busy;
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
   * @param int $duration
   */
  public function setDuration($duration)
  {
    $this->duration = \DateInterval::createFromDateString("$duration minutes");
    ;
  }

  public function calculateEnd()
  {
    $this->end = clone $this->start;
    $this->end->add($this->duration);
  }

}
