<?php

namespace AppBundle\Entity;

use AppBundle\Repository\RoutineLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=RoutineLogRepository::class)
 */
class RoutineLog
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Routine::class, inversedBy="routineLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routine;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Routine
     */
    public function getRoutine(): ?Routine
    {
        return $this->routine;
    }

    /**
     * @param Routine $routine
     * @return $this
     */
    public function setRoutine(?Routine $routine): self
    {
        $this->routine = $routine;

        return $this;
    }
}
