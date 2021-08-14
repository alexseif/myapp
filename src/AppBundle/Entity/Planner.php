<?php

namespace AppBundle\Entity;

use AppBundle\Repository\PlannerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=PlannerRepository::class)
 */
class Planner
{
    use TimestampableEntity;

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
     * @ORM\ManyToMany(targetEntity=Thing::class, inversedBy="planners", fetch="EAGER")
     */
    private $things;

    /**
     * @ORM\ManyToMany(targetEntity=TaskLists::class, inversedBy="planners", fetch="EAGER")
     */
    private $tasklists;


    /**
     * @ORM\ManyToMany(targetEntity=Objective::class, inversedBy="planners", fetch="EAGER")
     */
    private $objectives;

    public function __construct()
    {
        $this->things = new ArrayCollection();
        $this->tasklists = new ArrayCollection();
        $this->objectives = new ArrayCollection();
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

    /**
     * @return Collection|Thing[]
     */
    public function getThings(): Collection
    {
        return $this->things;
    }

    public function addThing(Thing $thing): self
    {
        if (!$this->things->contains($thing)) {
            $this->things[] = $thing;
            $thing->addPlanner($this);
        }

        return $this;
    }

    public function removeThing(Thing $thing): self
    {
        $this->things->removeElement($thing);

        return $this;
    }

    /**
     * @return Collection|TaskLists[]
     */
    public function getTasklists(): Collection
    {
        return $this->tasklists;
    }

    public function addTasklist(TaskLists $tasklist): self
    {
        if (!$this->tasklists->contains($tasklist)) {
            $this->tasklists[] = $tasklist;
        }

        return $this;
    }

    public function removeTasklist(TaskLists $tasklist): self
    {
        $this->tasklists->removeElement($tasklist);

        return $this;
    }

    /**
     * @return Collection|Objective[]
     */
    public function getObjectives(): Collection
    {
        return $this->objectives;
    }

    public function addObjective(Objective $objective): self
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives[] = $objective;
        }

        return $this;
    }

    public function removeObjective(Objective $objective): self
    {
        $this->objectives->removeElement($objective);

        return $this;
    }
}
