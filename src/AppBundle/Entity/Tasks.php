<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tasks
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TasksRepository")
 */
class Tasks
{

  const LOW_PRIORITY = -1;
  CONST NORMAL_PRIORITY = 0;
  CONST HIGH_PRIORITY = 1;
  CONST NORMAL_URGENCY = 0;
  CONST HIGH_URGENCY = 1;

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
   * @ORM\Column(name="task", type="string", length=255)
   */
  private $task;

  /**
   * @var integer
   *
   * @ORM\Column(name="torder", type="integer")
   */
  private $order;

  /**
   * @var integer
   *
   * @ORM\Column(name="priority", type="integer")
   */
  private $priority;

  /**
   * @var integer
   *
   * @ORM\Column(name="urgency", type="integer")
   */
  private $urgency;

  /**
   * @var boolean
   *
   * @ORM\Column(name="est", type="integer", nullable=true)
   */
  private $est;

  /**
   * @var datetime
   *
   * @ORM\Column(name="eta", type="datetime", nullable=true)
   */
  private $eta;

  /**
   * @var boolean
   *
   * @ORM\Column(name="completed", type="boolean")
   */
  private $completed;

  /**
   * @var datetime
   *
   * @ORM\Column(name="completedAt", type="datetime", nullable=true)
   */
  private $completedAt;

  /**
   * @var datetime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="TaskLists", inversedBy="tasks")
   * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
   */
  private $taskList;

  /**
   * Many Tasks have Many Costs.
   * @ORM\ManyToMany(targetEntity="Cost")
   * @ORM\JoinTable(name="tasks_costs",
   *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="cost_id", referencedColumnName="id", unique=true)}
   *      )
   */
  private $costs;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->costs = new \Doctrine\Common\Collections\ArrayCollection();
    $this->createdAt = new \DateTime();
    $this->order = 0;
    $this->priority = Tasks::NORMAL_PRIORITY;
    $this->urgency = Tasks::NORMAL_URGENCY;
    $this->priorityName = array(0 => "Not Important", 1 => "Important");
    $this->urgencyName = array(0 => "Not Urgent", 1 => "Urgent");
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set task
   *
   * @param string $task
   * @return Tasks
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
   * Set completed
   *
   * @param \DateTime $completed
   * @return Tasks
   */
  public function setCompleted($completed)
  {
    $this->completed = $completed;

    return $this;
  }

  /**
   * Get completed
   *
   * @return \DateTime 
   */
  public function getCompleted()
  {
    return $this->completed;
  }

  /**
   * Set completedAt
   *
   * @param \DateTime $completedAt
   * @return Tasks
   */
  public function setCompletedAt($completedAt)
  {
    $this->completedAt = $completedAt;

    return $this;
  }

  /**
   * Get completedAt
   *
   * @return \DateTime 
   */
  public function getCompletedAt()
  {
    return $this->completedAt;
  }

  /**
   * Set taskList
   *
   * @param \AppBundle\Entity\TaskLists $taskList
   * @return Tasks
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

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return Tasks
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return \DateTime 
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Set order
   *
   * @param integer $order
   * @return Tasks
   */
  public function setOrder($order)
  {
    $this->order = $order;

    return $this;
  }

  /**
   * Get order
   *
   * @return integer 
   */
  public function getOrder()
  {
    return $this->order;
  }

  /**
   * Set priority
   *
   * @param integer $priority
   * @return Tasks
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;

    return $this;
  }

  /**
   * Get priority
   *
   * @return integer 
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * Set urgency
   *
   * @param integer $urgency
   * @return Tasks
   */
  public function setUrgency($urgency)
  {
    $this->urgency = $urgency;

    return $this;
  }

  /**
   * Get urgency
   *
   * @return integer 
   */
  public function getUrgency()
  {
    return $this->urgency;
  }

  /**
   * Set est
   *
   * @param integer $est
   * @return Tasks
   */
  public function setEst($est)
  {
    $this->est = $est;

    return $this;
  }

  /**
   * Get est
   *
   * @return integer 
   */
  public function getEst()
  {
    return $this->est;
  }

  /**
   * Add cost
   *
   * @param \AppBundle\Entity\Cost $cost
   *
   * @return Tasks
   */
  public function addCost(\AppBundle\Entity\Cost $cost)
  {
    $this->costs[] = $cost;

    return $this;
  }

  /**
   * Remove cost
   *
   * @param \AppBundle\Entity\Cost $cost
   */
  public function removeCost(\AppBundle\Entity\Cost $cost)
  {
    $this->costs->removeElement($cost);
  }

  /**
   * Get costs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getCosts()
  {
    return $this->costs;
  }


    /**
     * Set eta
     *
     * @param \DateTime $eta
     *
     * @return Tasks
     */
    public function setEta($eta)
    {
        $this->eta = $eta;

        return $this;
    }

    /**
     * Get eta
     *
     * @return \DateTime
     */
    public function getEta()
    {
        return $this->eta;
    }
}
