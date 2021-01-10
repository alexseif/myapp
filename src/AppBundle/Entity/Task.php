<?php

namespace AppBundle\Entity;

use AppBundle\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    use \AppBundle\Entity\Traits\Item;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Item::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sizing;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $due;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $eta;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCompleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @ORM\ManyToOne(targetEntity="TaskLists", inversedBy="taskItems")
     * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
     */
    private $taskList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?item
    {
        return $this->item;
    }

    public function setItem(item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSizing(): ?int
    {
        return $this->sizing;
    }

    public function setSizing(?int $sizing): self
    {
        $this->sizing = $sizing;

        return $this;
    }

    public function getDue(): ?DateTimeInterface
    {
        return $this->due;
    }

    public function setDue(?DateTimeInterface $due): self
    {
        $this->due = $due;

        return $this;
    }

    public function getEta(): ?DateTimeInterface
    {
        return $this->eta;
    }

    public function setEta(?DateTimeInterface $eta): self
    {
        $this->eta = $eta;

        return $this;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Set taskList
     *
     * @param TaskLists $taskList
     * @return Task
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

}
