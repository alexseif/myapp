<?php

namespace AppBundle\Entity;

use AppBundle\Model\ContractProgress;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContractRepository")
 */
class Contract
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
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contracts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private $client;

    /**
     * @var int
     *
     * @ORM\Column(name="hoursPerDay", type="integer")
     */
    private $hoursPerDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startedAt", type="date")
     */
    private $startedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * Day of month to issue bill
     * @ORM\Column(type="integer", nullable=true)
     */
    private $billedOn = 25;

    /**
     * @var ContractProgress
     */
    private $progress;

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
     * @return Contract
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
     * Set account.
     *
     * @param \stdClass|null $account
     *
     * @return Contract
     */
    public function setAccount($account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account.
     *
     * @return \stdClass|null
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set hoursPerDay.
     *
     * @param int $hoursPerDay
     *
     * @return Contract
     */
    public function setHoursPerDay($hoursPerDay)
    {
        $this->hoursPerDay = $hoursPerDay;

        return $this;
    }

    /**
     * Get hoursPerDay.
     *
     * @return int
     */
    public function getHoursPerDay()
    {
        return $this->hoursPerDay;
    }

    /**
     * Set client.
     *
     * @param \AppBundle\Entity\Client|null $client
     *
     * @return Contract
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \AppBundle\Entity\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set startedAt.
     *
     * @param \DateTime $startedAt
     *
     * @return Contract
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt.
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     *
     * @return bool $isCompleted
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     *
     * @param bool $isCompleted
     * @return self
     */
    public function setIsCompleted(bool $isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     *
     * @return \DateTimeInterface $completedAt
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     *
     * @param \DateTimeInterface $completedAt
     * @return self
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getBilledOn(): ?int
    {
        return $this->billedOn;
    }

    public function setBilledOn(?int $billedOn): self
    {
        $this->billedOn = $billedOn;

        return $this;
    }

    /**
     * @return ContractProgress
     */
    public function getProgress(): ContractProgress
    {
        return $this->progress;
    }

    /**
     * @param ContractProgress $progress
     */
    public function setProgress(ContractProgress $progress): void
    {
        $this->progress = $progress;
    }

}
