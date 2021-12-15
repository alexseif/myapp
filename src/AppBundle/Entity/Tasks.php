<?php

namespace AppBundle\Entity;

use DateTime;
use DateTime as datetimeAlias;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use AppBundle\Repository\TasksRepository;

/**
 * Tasks
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass=TasksRepository::class)
 */
class Tasks
{

    use TimestampableEntity;

    const LOW_PRIORITY = -1;
    const NORMAL_PRIORITY = 0;
    const HIGH_PRIORITY = 1;
    const NORMAL_URGENCY = 0;
    const HIGH_URGENCY = 1;

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
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var integer
     *
     * @ORM\Column(name="est", type="integer", nullable=true)
     */
    private $est;

    /**
     * @var datetimeAlias
     *
     * @ORM\Column(name="eta", type="datetime", nullable=true)
     */
    private $eta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="completed", type="boolean")
     */
    private $completed = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="completedAt", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @ORM\ManyToOne(targetEntity="TaskLists", inversedBy="tasks")
     * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
     */
    private $taskList;

    /**
     * One Task has One WorkLog.
     * @ORM\OneToOne(targetEntity="WorkLog", mappedBy="task")
     */
    private $workLog;

    /**
     * If task is available for WorkLog.
     * @ORM\Column(name="work_loggable", type="boolean", options={"default": TRUE})
     */
    private $workLoggable;

    /**
     * @ORM\OneToOne(targetEntity=Schedule::class, mappedBy="task", cascade={"persist", "remove"})
     */
    private $schedule;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->costs = new ArrayCollection();
        $this->order = 0;
        $this->priority = Tasks::NORMAL_PRIORITY;
        $this->urgency = Tasks::NORMAL_URGENCY;
        $this->workLoggable = TRUE;
        $this->priorityName = array(
            -1 => "Low",
            0 => "Normal",
            1 => "Important"
        );
        $this->urgencyName = array(
            0 => "Normal",
            1 => "Urgent"
        );
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
     * @param bool $completed
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
     * @return bool
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
     * @return DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set taskList
     *
     * @param TaskLists $taskList
     * @return Tasks
     */
    public function setTaskList(TaskLists $taskList = null)
    {
        $this->taskList = $taskList;

        return $this;
    }

    /**
     * Get taskList
     *
     * @return TaskLists
     */
    public function getTaskList()
    {
        return $this->taskList;
    }

    /**
     * Get Account
     *
     * @return \AppBundle\Entity\Accounts
     */
    public function getAccount()
    {
        return $this->getTaskList()->getAccount();
    }

    /**
     * Get Client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return ($this->getTaskList()->getAccount()) ? $this->getTaskList()->getAccount()->getClient() : null;
    }

    /**
     * Get Rate
     *
     * @return \AppBundle\Entity\Rate
     */
    public function getRate()
    {
        return ($this->getTaskList()->getAccount()) ? $this->getTaskList()->getAccount()->getClient()->getRate() : null;
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
     * Get priority
     *
     * @return string
     */
    public function getPriorityName()
    {
        $priorityName = array(
            -1 => "Low",
            0 => "Normal",
            1 => "Important"
        );
        return $priorityName[$this->priority];
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
     * Get urgency
     *
     * @return string
     */
    public function getUrgencyName()
    {
        $urgencyName = array(
            0 => "Normal",
            1 => "Urgent"
        );
        return $urgencyName[$this->urgency];
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Tasks
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
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
     * @return DateTime
     */
    public function getEta()
    {
        return $this->eta;
    }

    /**
     * Set workLog
     *
     * @param WorkLog $workLog
     *
     * @return Tasks
     */
    public function setWorkLog(WorkLog $workLog = null)
    {
        $this->workLog = $workLog;

        return $this;
    }

    /**
     * Get workLog
     *
     * @return WorkLog
     */
    public function getWorkLog()
    {
        return $this->workLog;
    }

    /**
     * Set workLoggable
     *
     * @param boolean $workLoggable
     *
     * @return Tasks
     */
    public function setWorkLoggable($workLoggable)
    {
        $this->workLoggable = $workLoggable;

        return $this;
    }

    /**
     * Get workLoggable
     *
     * @return boolean
     */
    public function getWorkLoggable()
    {
        return $this->workLoggable;
    }

    public function getName()
    {
        return $this->getTask();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set est.
     *
     * @param int|null $est
     *
     * @return Tasks
     */
    public function setEst($est = null)
    {
        $this->est = $est;

        return $this;
    }

    /**
     * Get est.
     *
     * @return int|null
     */
    public function getEst()
    {
        return $this->est;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(Schedule $schedule): self
    {
        // set the owning side of the relation if necessary
        if ($schedule->getTask() !== $this) {
            $schedule->setTask($this);
        }

        $this->schedule = $schedule;

        return $this;
    }

}
