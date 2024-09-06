<?php

namespace App\Entity;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * TaskLists.
 *
 * @ORM\Table(name="task_lists")
 * @ORM\Entity(repositoryClass="App\Repository\TaskListsRepository")
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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="taskLists")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=true)
     */
    private ?Accounts $account;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private string $status;

    /**
     * @ORM\OneToMany(targetEntity="Tasks", mappedBy="taskList", cascade={"remove"})
     * @ORM\OrderBy({"completed" = "ASC", "order" = "ASC"})
     */
    private Collection $tasks;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->status = 'start';
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
     * Set name.
     *
     * @param string $name
     *
     * @return TaskLists
     */
    public function setName(string $name): TaskLists
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return TaskLists
     */
    public function setStatus(string $status): TaskLists
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Add tasks.
     *
     */
    public function addTask(Tasks $tasks): TaskLists
    {
        $tasks->setTaskList($this);
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks.
     */
    public function removeTask(Tasks $tasks): void
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks.
     *
     */
    public function getTasks($showComplete = true): Collection
    {
        $criteria = Criteria::create();
        if (true !== $showComplete) {
            $criteria->where(Criteria::expr()->eq('completed', false));
        }
        $criteria->orderBy([
          'completed' => 'asc',
          'urgency' => 'desc',
          'priority' => 'desc',
        ]);

        return $this->tasks->matching($criteria);
    }

    public function getDurationTotal($showComplete = true)
    {
        $durationTotal = 0;
        foreach ($this->getTasks($showComplete) as $task) {
            $durationTotal += $task->getDuration();
        }
        $today = new DateTime();
        $endDay = new DateTime();
        $endDay->add(
          DateInterval::createFromDateString($durationTotal . ' minutes')
        );

        return $endDay->diff($today);
    }

    public function getEstTotal($showComplete = false)
    {
        $estTotal = 0;
        foreach ($this->getTasks($showComplete) as $task) {
            $estTotal += ($task->getEst()) ?: 60;
        }
        $today = new DateTime();
        $endDay = new DateTime();
        $endDay->add(
          DateInterval::createFromDateString($estTotal . ' minutes')
        );

        return $endDay->diff($today);
    }

    /**
     * Set account.
     *
     * @param \App\Entity\Accounts|null $account
     *
     * @return TaskLists
     */
    public function setAccount(Accounts $account = null): TaskLists
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account.
     *
     */
    public function getAccount(): Accounts|null
    {
        return $this->account;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
