<?php

namespace AppBundle\Entity;

use AppBundle\Repository\RoutineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=RoutineRepository::class)
 */
class Routine
{
    use TimestampableEntity;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $priority = 0;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $sort = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $status = "active";

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity=RoutineLog::class, mappedBy="routine", orphanRemoval=true)
     */
    private $routineLogs;

    /**
     * Routine constructor.
     */
    public function __construct()
    {
        $this->routineLogs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     * @return $this
     */
    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|RoutineLog[]
     */
    public function getRoutineLogs(): Collection
    {
        return $this->routineLogs;
    }

    /**
     * @param RoutineLog $routineLog
     * @return $this
     */
    public function addRoutineLog(RoutineLog $routineLog): self
    {
        if (!$this->routineLogs->contains($routineLog)) {
            $this->routineLogs[] = $routineLog;
            $routineLog->setRoutine($this);
        }

        return $this;
    }

    /**
     * @param RoutineLog $routineLog
     * @return $this
     */
    public function removeRoutineLog(RoutineLog $routineLog): self
    {
        if ($this->routineLogs->removeElement($routineLog)) {
            // set the owning side to null (unless already changed)
            if ($routineLog->getRoutine() === $this) {
                $routineLog->setRoutine(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

}
