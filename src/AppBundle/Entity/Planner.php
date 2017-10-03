<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Planner
 *
 * @ORM\Table(name="planner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlannerRepository")
 */
class Planner
{

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, unique=true)
   */
  private $name;

  /**
   * Many Planner have Many Tasks.
   * @ORM\ManyToMany(targetEntity="Tasks")
   * @ORM\JoinTable(name="planner_tasks",
   *      joinColumns={@ORM\JoinColumn(name="planner_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id", unique=true)}
   *      )
   * @ORM\OrderBy({"completed" = "ASC","taskList" = "ASC", "urgency" = "DESC", "priority" = "DESC", "order" = "ASC"})
   */
  private $tasks;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="due", type="datetime")
   */
  private $due;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->due = new \DateTime();
    $this->name = $this->due->format('Y-m-d');
    $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
  }

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
   * Set name
   *
   * @param string $name
   *
   * @return Planner
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set task
   *
   * @param string $task
   *
   * @return Planner
   */
  public function setTask($task)
  {
    $this->task = $task;

    return $this;
  }

  /**
   * Get task
   *
   * @return string
   */
  public function getTask()
  {
    return $this->task;
  }

  /**
   * Set torder
   *
   * @param integer $torder
   *
   * @return Planner
   */
  public function setTorder($torder)
  {
    $this->torder = $torder;

    return $this;
  }

  /**
   * Get torder
   *
   * @return int
   */
  public function getTorder()
  {
    return $this->torder;
  }

  /**
   * Set due
   *
   * @param \DateTime $due
   *
   * @return Planner
   */
  public function setDue($due)
  {
    $this->due = $due;

    return $this;
  }

  /**
   * Get due
   *
   * @return \DateTime
   */
  public function getDue()
  {
    return $this->due;
  }

  /**
   * Add task
   *
   * @param \AppBundle\Entity\PlannerTasks $task
   *
   * @return Planner
   */
  public function addTask(\AppBundle\Entity\PlannerTasks $task)
  {
    $this->tasks[] = $task;

    return $this;
  }

  /**
   * Remove task
   *
   * @param \AppBundle\Entity\PlannerTasks $task
   */
  public function removeTask(\AppBundle\Entity\PlannerTasks $task)
  {
    $this->tasks->removeElement($task);
  }

  /**
   * Get tasks
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTasks()
  {
    return $this->tasks;
  }

  /**
   * Get tasks
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getCompletedTasks()
  {
    $completedTasks = new \Doctrine\Common\Collections\ArrayCollection();
    foreach ($this->tasks as $task) {
      if ($task->getCompleted()) {
        $completedTasks->add($task);
      }
    }
    return $completedTasks;
  }

}
