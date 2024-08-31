<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Rate.
 *
 * @ORM\Table(name="rate")
 * @ORM\Entity(repositoryClass="App\Repository\RateRepository")
 */
class Rate
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
     * @var float
     *
     * @ORM\Column(name="rate", type="float")
     */
    private $rate;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="rates")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

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
     * Set rate.
     *
     * @param float $rate
     *
     * @return Rate
     */
    public function setRate($rate): Rate
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate.
     *
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * Set client.
     *
     * @paramClient|null $client
     *
     * @return Rate
     */
    public function setClient(Client $client = null): Rate
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
     * Set active.
     *
     * @param bool $active
     *
     * @return Rate
     */
    public function setActive($active): Rate
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Set note.
     *
     * @param string|null $note
     *
     * @return Rate
     */
    public function setNote($note = null): Rate
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }
}
