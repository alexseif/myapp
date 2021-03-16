<?php

namespace AppBundle\Entity;

use AppBundle\Repository\TaskListsRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TaskListsRepository::class)
 */
class TaskLists
{

    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Accounts::class, inversedBy="taskLists")
     * @ORM\JoinColumn(nullable=true)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;


    /**
     * @ORM\OneToMany(targetEntity=Tasks::class, mappedBy="taskList", cascade="remove")
     * @ORM\OrderBy({"completed" = "ASC", "order" = "ASC"})
     */
    private $tasks;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity=Feature::class, mappedBy="list, orphanRemoval=true")
     */
    private $features;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->status = "start";
        $this->features = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }


    public function addTask(Tasks $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskList($this);
        }

        return $this;
    }


    public function removeTask(Tasks $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskList() === $this) {
                $task->setTaskList(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Tasks[]
     */
    public function getTasks($showComplete = true): Collection
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

//        return $this->tasks;
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


    public function setAccount(?Accounts $account): self
    {
        $this->account = $account;

        return $this;
    }


    public function getAccount(): ?Accounts
    {
        return $this->account;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Feature[]
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features[] = $feature;
            $feature->setList($this);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getList() === $this) {
                $feature->setList(null);
            }
        }

        return $this;
    }
}
