<?php

namespace AppBundle\Entity;

use AppBundle\Definition\Types;
use AppBundle\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority = 0;
    private $priorityClasses = [
        0 => 'default',
        1 => 'info',
        2 => 'warning',
        3 => 'danger'
    ];

    /**
     * @ORM\Column(type="integer")
     */
    private $sort = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type = Types::DEFAULT;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPriorityClass()
    {
        return (array_key_exists($this->priority, $this->priorityClasses)) ? $this->priorityClasses[$this->priority] : 'danger';
    }

    /**
     * @return string[]
     */
    public function getPriorityClasses(): array
    {
        return $this->priorityClasses;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
