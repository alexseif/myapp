<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Days.
 *
 * @ORM\Table(name="days")
 * @ORM\Entity(repositoryClass="App\Repository\DaysRepository")
 */
class Days
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
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="date")
     */
    private $deadline;

    /**
     * @var bool
     *
     * @ORM\Column(name="complete", type="boolean")
     */
    private $complete;

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
     * @return Days
     */
    public function setName($name): Days
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
     * Set deadline.
     *
     * @param \DateTime $deadline
     *
     * @return Days
     */
    public function setDeadline($deadline): Days
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline.
     *
     * @return \DateTime
     */
    public function getDeadline(): DateTime
    {
        return $this->deadline;
    }

    /**
     * Set complete.
     *
     * @param bool $complete
     *
     * @return Days
     */
    public function setComplete($complete): Days
    {
        $this->complete = $complete;

        return $this;
    }

    /**
     * Get complete.
     *
     * @return bool
     */
    public function getComplete(): bool
    {
        return $this->complete;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
