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
  protected $timestamp;

  /**
   *
   * @var string
   */
  protected $description;

  /**
   *
   * @var int
   */
  protected $urgency;

  /**
   *
   * @var int
   */
  protected $priority;

  /**
   *
   * @var string
   */
  protected $type;

  /**
   * 
   * @param string $title
   * @param \DateTime $timestamp
   * @param string $description
   * @param int $urgency
   * @param int $priority
   * @param string $type
   */
  public function __construct($title, \DateTime $timestamp, $description, $urgency, $priority, $type)
  {
    $this->title = $title;
    $this->timestamp = $timestamp;
    $this->description = $description;
    $this->urgency = $urgency;
    $this->priority = $priority;
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
  public function getTimestamp()
  {
    return $this->timestamp;
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
   * @param \DateTime $timestamp
   */
  public function setTimestamp(\DateTime $timestamp)
  {
    $this->timestamp = $timestamp;
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

}
