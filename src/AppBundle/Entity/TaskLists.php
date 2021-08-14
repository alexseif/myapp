<?php

namespace AppBundle\Entity;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * TaskLists
 *
 * @ORM\Table(name="task_lists")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskListsRepository")
 */
class TaskLists
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
     * @ORM\OneToMany(targetEntity="Tasks", mappedBy="taskList", cascade="remove")
     * @ORM\OrderBy({"completed" = "ASC", "order" = "ASC"})
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity=Planner::class, mappedBy="tasklists")
     */
    private $planners;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->status = "start";
        $this->planners = new ArrayCollection();
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
     * Add tasks
     *
     * @param Tasks $tasks
     * @return TaskLists
     */
    public function addTask(Tasks $tasks)
    {
        $tasks->setTaskList($this);
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param Tasks $tasks
     */
    public function removeTask(Tasks $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return Collection
     */
    public function getTasks($showComplete = true)
    {
        $criteria = Criteria::create();
        if ($showComplete !== true) {
            $criteria->where(Criteria::expr()->eq('completed', false));
        }
        $criteria->orderBy(array(
            'completed' => 'asc',
            'urgency' => 'desc',
            'priority' => 'desc'
        ));
        return $this->tasks->matching($criteria);
    }

    public function getDurationTotal($showComplete = true)
    {
        $durationTotal = 0;
        $tasks = $this->getTasks($showComplete);
        foreach ($tasks as $task) {
            $durationTotal += $task->getDuration();
        }
        $today = new DateTime();
        $endDay = new DateTime();
        $endDay->add(DateInterval::createFromDateString($durationTotal . " minutes"));
        return $endDay->diff($today);
    }

    /**
     * Set account
     *
     * @param Accounts $account
     *
     * @return TaskLists
     */
    public function setAccount(Accounts $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Accounts
     */
    public function getAccount()
    {
        return $this->account;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|Planner[]
     */
    public function getPlanners(): Collection
    {
        return $this->planners;
    }

    public function addPlanner(Planner $planner): self
    {
        if (!$this->planners->contains($planner)) {
            $this->planners[] = $planner;
            $planner->addTasklist($this);
        }

        return $this;
    }

    public function removePlanner(Planner $planner): self
    {
        if ($this->planners->removeElement($planner)) {
            $planner->removeTasklist($this);
        }

        return $this;
    }


}
