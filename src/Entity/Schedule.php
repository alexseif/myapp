<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\OneToOne(targetEntity=Tasks::class, inversedBy="schedule")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Tasks $task;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $est;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $eta;

    /**
     * @param $id
     * @param $task
     * @param $est
     * @param $eta
     */
    public function setSchedule($id, $task, $est, $eta): void
    {
        $this->id = $id;
        $this->task = $task;
        $this->est = ($est) ?: 60;
        $this->eta = $eta;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Tasks
    {
        return $this->task;
    }

    public function setTask(Tasks $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getEst(): ?int
    {
        return $this->est;
    }

    public function setEst(int $est): self
    {
        $this->est = $est;

        return $this;
    }

    public function getEta(): ?DateTimeInterface
    {
        return $this->eta;
    }

    public function setEta(DateTimeInterface $eta): self
    {
        $this->eta = $eta;

        return $this;
    }

}
