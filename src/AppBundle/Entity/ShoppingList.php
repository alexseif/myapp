<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ShoppingListRepository::class)
 */
class ShoppingList
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
     * @ORM\OneToMany(targetEntity=ShoppingItem::class, mappedBy="ShoppingList")
     */
    private $shoppingItems;

    public function __construct()
    {
        $this->shoppingItems = new ArrayCollection();
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
     * @return Collection|ShoppingItem[]
     */
    public function getShoppingItems(): Collection
    {
        return $this->shoppingItems;
    }

    public function addShoppingItem(ShoppingItem $shoppingItem): self
    {
        if (!$this->shoppingItems->contains($shoppingItem)) {
            $this->shoppingItems[] = $shoppingItem;
            $shoppingItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingItem(ShoppingItem $shoppingItem): self
    {
        if ($this->shoppingItems->removeElement($shoppingItem)) {
            // set the owning side to null (unless already changed)
            if ($shoppingItem->getShoppingList() === $this) {
                $shoppingItem->setShoppingList(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
