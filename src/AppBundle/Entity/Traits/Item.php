<?php


namespace AppBundle\Entity\Traits;

/**
 * Trait Item
 * @package AppBundle\Entity\Traits
 */
trait Item
{
    public function getItemId(): ?int
    {
        return $this->getItem()->getId();
    }

    public function getTitle(): ?string
    {
        return $this->getItem()->getTitle();
    }

    public function setTitle(string $title): self
    {
        $this->getItem()->setTitle($title);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->getItem()->getDescription();
    }

    public function setDescription(?string $description): self
    {
        $this->getItem()->setDescription($description);

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->getItem()->getPriority();
    }

    public function setPriority(int $priority): self
    {
        $this->getItem()->setPriority($priority);

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->getItem()->getSort();
    }

    public function setSort(int $sort): self
    {
        $this->getItem()->setSort($sort);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->getItem()->geType();
    }

    public function setType(string $type): self
    {
        $this->getItem()->setType($type);

        return $this;
    }

    public function getPriorityClass()
    {
        return (array_key_exists($this->getItem()->getPriority(), $this->getItem()->getPriorityClasses())) ? $this->getItem()->getPriorityClasses()[$this->getItem()->getPriority()] : 'danger';
    }


    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getItem()->getCreatedAt();
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->getItem()->getUpdatedAt();
    }
}