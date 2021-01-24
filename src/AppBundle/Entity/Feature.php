<?php

namespace AppBundle\Entity;

use AppBundle\Repository\FeatureRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=FeatureRepository::class)
 */
class Feature
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

    /**
     * @ORM\Column(type="integer")
     */
    private $sort = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $est;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $offer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isApproved;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @ORM\ManyToOne(targetEntity=TaskLists::class, inversedBy="features")
     * @ORM\JoinColumn(nullable=false)
     */
    private $list;

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

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
    {
        $this->rate = $rate;

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

    public function getOffer(): ?float
    {
        return $this->offer;
    }

    public function setOffer(?float $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getApprovedAt(): ?DateTimeInterface
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?DateTimeInterface $approvedAt): self
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getList(): ?TaskLists
    {
        return $this->list;
    }

    public function setList(?TaskLists $list): self
    {
        $this->list = $list;

        return $this;
    }
}
