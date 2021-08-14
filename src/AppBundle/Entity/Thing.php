<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Thing
 *
 * @ORM\Table(name="thing")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThingRepository")
 */
class Thing
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
     * @ORM\ManyToMany(targetEntity=Planner::class, mappedBy="things")
     */
    private $planners;

    public function __construct()
    {
        $this->planners = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Thing
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
            $planner->addThing($this);
        }

        return $this;
    }

    public function removePlanner(Planner $planner): self
    {
        if ($this->planners->removeElement($planner)) {
            $planner->removeThing($this);
        }

        return $this;
    }


}
