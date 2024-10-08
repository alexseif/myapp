<?php

namespace App\Entity;

use App\Model\ContractProgress;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Contract.
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="App\Repository\ContractRepository")
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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contracts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private ?Client $client;

    /**
     * @var int
     *
     * @ORM\Column(name="hoursPerDay", type="integer")
     */
    private int $hoursPerDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startedAt", type="date")
     */
    private DateTime $startedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCompleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTimeInterface $completedAt;

    /**
     * Day of month to issue bill.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private int|null $billedOn = 25;

    /**
     * @var ContractProgress
     */
    private ContractProgress $progress;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
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
    public function setName(string $name): Contract
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Set hoursPerDay.
     *
     * @param int $hoursPerDay
     *
     * @return Contract
     */
    public function setHoursPerDay(int $hoursPerDay): Contract
    {
        $this->hoursPerDay = $hoursPerDay;

        return $this;
    }

    /**
     * Get hoursPerDay.
     *
     * @return int
     */
    public function getHoursPerDay(): int
    {
        return $this->hoursPerDay;
    }

    /**
     * Set client.
     *
     * @param Client|null $client
     *
     */
    public function setClient(Client $client = null): Contract
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     */
    public function getClient(): ?Client
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
    public function setStartedAt(DateTime $startedAt): Contract
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt.
     *
     * @return \DateTime
     */
    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return bool $isCompleted
     */
    public function getIsCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * @return \DateTimeInterface $completedAt
     */
    public function getCompletedAt(): DateTimeInterface
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTimeInterface|null $completedAt
     *
     * @return self
     */
    public function setCompletedAt(?DateTimeInterface $completedAt): self
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

    public function getProgress(): ContractProgress
    {
        return $this->progress;
    }

    public function setProgress(ContractProgress $progress): void
    {
        $this->progress = $progress;
    }

}
