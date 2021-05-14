<?php

namespace AppBundle\Entity;

use AppBundle\Repository\HabitLogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HabitLogRepository::class)
 */
class HabitLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Habit::class, inversedBy="habitLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $habit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHabit(): ?Habit
    {
        return $this->habit;
    }

    public function setHabit(?Habit $habit): self
    {
        $this->habit = $habit;

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
}
