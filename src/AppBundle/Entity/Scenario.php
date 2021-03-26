<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ScenarioRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScenarioRepository", repositoryClass=ScenarioRepository::class)
 */
class Scenario
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
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=ScenarioDetails::class, mappedBy="scenario", orphanRemoval=true)
     * @ORM\OrderBy({"date"="ASC"})
     */
    private $scenarioDetails;

    /**
     * @ORM\OneToMany(targetEntity=ScenarioObjective::class, mappedBy="scenario", orphanRemoval=true)
     * @ORM\OrderBy({"urgency"="DESC", "priority"="DESC"})
     */
    private $scenarioObjectives;

    public function __construct()
    {
        $date = new DateTime();
        $this->title = $date->format('Y-m-d');
        $this->scenarioDetails = new ArrayCollection();
        $this->scenarioObjectives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|ScenarioDetails[]
     */
    public function getScenarioDetails(): Collection
    {
        return $this->scenarioDetails;
    }

    public function addScenarioDetail(ScenarioDetails $scenarioDetail): self
    {
        if (!$this->scenarioDetails->contains($scenarioDetail)) {
            $this->scenarioDetails[] = $scenarioDetail;
            $scenarioDetail->setScenario($this);
        }

        return $this;
    }

    public function removeScenarioDetail(ScenarioDetails $scenarioDetail): self
    {
        if ($this->scenarioDetails->removeElement($scenarioDetail)) {
            // set the owning side to null (unless already changed)
            if ($scenarioDetail->getScenario() === $this) {
                $scenarioDetail->setScenario(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return Collection|ScenarioObjective[]
     */
    public function getScenarioObjectives(): Collection
    {
        return $this->scenarioObjectives;
    }

    public function addScenarioObjective(ScenarioObjective $scenarioObjective): self
    {
        if (!$this->scenarioObjectives->contains($scenarioObjective)) {
            $this->scenarioObjectives[] = $scenarioObjective;
            $scenarioObjective->setScenario($this);
        }

        return $this;
    }

    public function removeScenarioObjective(ScenarioObjective $scenarioObjective): self
    {
        if ($this->scenarioObjectives->removeElement($scenarioObjective)) {
            // set the owning side to null (unless already changed)
            if ($scenarioObjective->getScenario() === $this) {
                $scenarioObjective->setScenario(null);
            }
        }

        return $this;
    }


}
