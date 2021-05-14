<?php

namespace AppBundle\Entity;

use AppBundle\Repository\HabitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HabitRepository::class)
 */
class Habit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = 'active';

    /**
     * @ORM\OneToMany(targetEntity=HabitLog::class, mappedBy="habit", orphanRemoval=true)
     */
    private $habitLogs;

    public function __construct()
    {
        $this->habitLogs = new ArrayCollection();
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|HabitLog[]
     */
    public function getHabitLogs(): Collection
    {
        return $this->habitLogs;
    }

    public function addHabitLog(HabitLog $habitLog): self
    {
        if (!$this->habitLogs->contains($habitLog)) {
            $this->habitLogs[] = $habitLog;
            $habitLog->setHabit($this);
        }

        return $this;
    }

    public function removeHabitLog(HabitLog $habitLog): self
    {
        if ($this->habitLogs->removeElement($habitLog)) {
            // set the owning side to null (unless already changed)
            if ($habitLog->getHabit() === $this) {
                $habitLog->setHabit(null);
            }
        }

        return $this;
    }
}
