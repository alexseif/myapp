<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * DashboardTaskLists
 *
 * @ORM\Table(name="dashboard_task_lists")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DashboardTaskListsRepository")
 */
class DashboardTaskLists
{

  use TimestampableEntity;

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * 
   * @ORM\OneToOne(targetEntity="TaskLists")
   * @ORM\JoinColumn(name="taskLists_id", referencedColumnName="id")
   */
  private $taskList;

  /**
   * Get id
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set taskList
   *
   * @param \AppBundle\Entity\TaskLists $taskList
   *
   * @return DashboardTaskLists
   */
  public function setTaskList(\AppBundle\Entity\TaskLists $taskList = null)
  {
    $this->taskList = $taskList;

    return $this;
  }

  /**
   * Get taskList
   *
   * @return \AppBundle\Entity\TaskLists
   */
  public function getTaskList()
  {
    return $this->taskList;
  }

  public function getName()
  {
    return $this->taskList->getName();
  }

}
