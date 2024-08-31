<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use DateTime;
use DateTime as datetimeAlias;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Tasks.
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass=TasksRepository::class)
 */
class Tasks
{

    use TimestampableEntity;

    public const LOW_PRIORITY = -1;

    public const NORMAL_PRIORITY = 0;

    public const HIGH_PRIORITY = 1;

    public const NORMAL_URGENCY = 0;

    public const HIGH_URGENCY = 1;

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
     * @var int
     *
     * @ORM\Column(name="torder", type="integer")
     */
    private $order;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="urgency", type="integer")
     */
    private $urgency;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var int
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
     * @var bool
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
     *
     * @ORM\OneToOne(targetEntity="WorkLog", mappedBy="task")
     */
    private $workLog;

    /**
     * If task is available for WorkLog.
     *
     * @ORM\Column(name="work_loggable", type="boolean", options={"default": TRUE})
     */
    private $workLoggable;

    /**
     * @ORM\OneToOne(targetEntity=Schedule::class, mappedBy="task", cascade={"persist", "remove"})
     */
    private $schedule;

    /**
     * @var string[]
     */
    public $priorityName;

    /**
     * @var string[]
     */
    public $urgencyName;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->order = 0;
        $this->priority = Tasks::NORMAL_PRIORITY;
        $this->urgency = Tasks::NORMAL_URGENCY;
        $this->workLoggable = true;
        $this->priorityName = [
          -1 => 'Low',
          0 => 'Normal',
          1 => 'Important',
        ];
        $this->urgencyName = [
          0 => 'Normal',
          1 => 'Urgent',
        ];
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set task.
     *
     * @param string $task
     *
     * @return Tasks
     */
    public function setTask($task): Tasks
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task.
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * Set completed.
     *
     * @param bool $completed
     *
     * @return Tasks
     */
    public function setCompleted($completed): Tasks
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed.
     *
     * @return bool
     */
    public function getCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * Set completedAt.
     *
     * @param \DateTime|null $completedAt
     *
     * @return Tasks
     */
    public function setCompletedAt($completedAt): Tasks
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt.
     *
     * @return DateTime
     */
    public function getCompletedAt(): DateTime
    {
        return $this->completedAt;
    }

    /**
     * Set taskList.
     *
     * @param TaskLists $taskList
     *
     * @return Tasks
     */
    public function setTaskList(TaskLists $taskList = null): Tasks
    {
        $this->taskList = $taskList;

        return $this;
    }

    /**
     * Get taskList.
     *
     */
    public function getTaskList(): TaskLists|null
    {
        return $this->taskList;
    }

    /**
     * Get Account.
     *
     */
    public function getAccount(): Accounts
    {
        return $this->getTaskList()->getAccount();
    }

    /**
     * Get Client.
     *
     */
    public function getClient(): Client|null
    {
        return ($this->getTaskList()->getAccount()) ? $this->getTaskList()
          ->getAccount()
          ->getClient() : null;
    }

    /**
     * Get Rate.
     *
     */
    public function getRate(): ?float
    {
        return ($this->getTaskList()->getAccount()) ? $this->getTaskList()
          ->getAccount()
          ->getClient()
          ->getRate() : null;
    }

    /**
     * Set order.
     *
     * @param int $order
     *
     * @return Tasks
     */
    public function setOrder($order): Tasks
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Set priority.
     *
     * @param int $priority
     *
     * @return Tasks
     */
    public function setPriority($priority): Tasks
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriorityName(): string
    {
        return $this->priorityName[$this->priority];
    }

    /**
     * Set urgency.
     *
     * @param int $urgency
     *
     * @return Tasks
     */
    public function setUrgency($urgency): Tasks
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency.
     *
     * @return int
     */
    public function getUrgency(): int
    {
        return $this->urgency;
    }

    /**
     * Get urgency.
     *
     * @return string
     */
    public function getUrgencyName(): string
    {
        return $this->urgencyName[$this->urgency];
    }

    /**
     * Set duration.
     *
     * @param int $duration
     *
     * @return Tasks
     */
    public function setDuration($duration): Tasks
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     *
     */
    public function getDuration(): int|null
    {
        return $this->duration;
    }

    /**
     * Set eta.
     *
     * @param \DateTime $eta
     *
     * @return Tasks
     */
    public function setEta($eta): Tasks
    {
        $this->eta = $eta;

        return $this;
    }

    /**
     * Get eta.
     *
     */
    public function getEta(): DateTime|null
    {
        return $this->eta;
    }

    /**
     * Set workLog.
     *
     * @param WorkLog $workLog
     *
     * @return Tasks
     */
    public function setWorkLog(WorkLog $workLog = null): Tasks
    {
        $this->workLog = $workLog;

        return $this;
    }

    /**
     * Get workLog.
     *
     * @return WorkLog
     */
    public function getWorkLog(): WorkLog
    {
        return $this->workLog;
    }

    /**
     * Set workLoggable.
     *
     * @param bool $workLoggable
     *
     * @return Tasks
     */
    public function setWorkLoggable($workLoggable): Tasks
    {
        $this->workLoggable = $workLoggable;

        return $this;
    }

    /**
     * Get workLoggable.
     *
     * @return bool
     */
    public function getWorkLoggable(): bool
    {
        return $this->workLoggable;
    }

    public function getName(): string
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
    public function setEst($est = null): Tasks
    {
        $this->est = $est;

        return $this;
    }

    /**
     * Get est.
     *
     * @return int|null
     */
    public function getEst(): ?int
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
