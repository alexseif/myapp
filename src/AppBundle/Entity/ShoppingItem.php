<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ShoppingItemRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ShoppingItemRepository::class)
 */
class ShoppingItem
{
    use TimestampableEntity;

    const LOW_PRIORITY = -1;
    const NORMAL_PRIORITY = 0;
    const HIGH_PRIORITY = 1;
    const NORMAL_URGENCY = 0;
    const HIGH_URGENCY = 1;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $est;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\Column(type="integer")
     */
    private $urgency;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $eta;

    /**
     * @ORM\ManyToOne(targetEntity=ShoppingList::class, inversedBy="shoppingItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ShoppingList;

    /**
     * ShoppingItem constructor.
     * @param $id
     */
    public function __construct()
    {
        $this->priority = ShoppingItem::NORMAL_PRIORITY;
        $this->urgency = ShoppingItem::NORMAL_URGENCY;
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

    public function getEst(): ?int
    {
        return $this->est;
    }

    public function setEst(?int $est): self
    {
        $this->est = $est;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getUrgency(): ?int
    {
        return $this->urgency;
    }

    public function setUrgency(int $urgency): self
    {
        $this->urgency = $urgency;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getEta(): ?DateTimeInterface
    {
        return $this->eta;
    }

    public function setEta(?DateTimeInterface $eta): self
    {
        $this->eta = $eta;

        return $this;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->ShoppingList;
    }

    public function setShoppingList(?ShoppingList $ShoppingList): self
    {
        $this->ShoppingList = $ShoppingList;

        return $this;
    }
}
