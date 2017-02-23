<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskLists
 *
 * @ORM\Table(name="task_lists")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskListsRepository")
 */
class TaskLists
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
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="taskLists")
   * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=true)
   */
  private $account;

  /**
   * @var string
   *
   * @ORM\Column(name="status", type="string", length=255)
   */
  private $status;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\OneToMany(targetEntity="Tasks", mappedBy="taskList", cascade="remove")
   * @ORM\OrderBy({"completed" = "ASC", "order" = "ASC"})
   */
  private $tasks;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    $this->createdAt = new \DateTime();
    $this->status = "start";
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
   * Set name
   *
   * @param string $name
   * @return TaskLists
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
   * Set status
   *
   * @param string $status
   * @return TaskLists
   */
  public function setStatus($status)
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return string 
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return TaskLists
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
   * Add tasks
   *
   * @param \AppBundle\Entity\Tasks $tasks
   * @return TaskLists
   */
  public function addTask(\AppBundle\Entity\Tasks $tasks)
  {
    $tasks->setTaskList($this);
    $this->tasks[] = $tasks;

    return $this;
  }

  /**
   * Remove tasks
   *
   * @param \AppBundle\Entity\Tasks $tasks
   */
  public function removeTask(\AppBundle\Entity\Tasks $tasks)
  {
    $this->tasks->removeElement($tasks);
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

  public function getEstTotal()
  {
    $estTotal = 0;
    $tasks = $this->getTasks();
    foreach ($tasks as $task) {
      $estTotal += $task->getEst();
    }
    $today = new \DateTime();
    $endDay = new \DateTime();
    $endDay->add(\DateInterval::createFromDateString($estTotal . " minutes"));
    $interval = $endDay->diff($today);

    return $interval;
  }


    /**
     * Set account
     *
     * @param \AppBundle\Entity\Accounts $account
     *
     * @return TaskLists
     */
    public function setAccount(\AppBundle\Entity\Accounts $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \AppBundle\Entity\Accounts
     */
    public function getAccount()
    {
        return $this->account;
    }
}
