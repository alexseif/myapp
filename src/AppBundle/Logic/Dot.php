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
  protected $title;

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
   * @var string
   */
  protected $type;

  public function __construct($title, $type, \DateInterval $duration)
  {
    $this->title = $title;
    $this->duration = $duration;
    $this->type = $type;
  }

  /**
   * 
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
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
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
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
   * @param \DateInterval $duration
   */
  public function setDuration(\DateInterval $duration)
  {
    $this->duration = $duration;
  }

  public function calculateEnd()
  {
    $this->end = clone $this->start;
    $this->end->add($this->duration);
  }

}
