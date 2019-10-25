<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

/**
 * Description of ActionItem
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Planner
{

  protected $date;
  protected $tasks = [];

  public function getDate()
  {
    return $this->date;
  }

  public function getTasks()
  {
    return $this->tasks;
  }

  public function setDate($date)
  {
    $this->date = $date;
  }

  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }

}
