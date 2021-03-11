<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ProposalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ProposalRepository::class)
 */
class Proposal
{
    use TimestampableEntity;
    use Traits\Status;

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
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=ProposalDetails::class, mappedBy="proposal")
     */
    private $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|ProposalDetails[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(ProposalDetails $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setProposal($this);
        }

        return $this;
    }

    public function removeDetail(ProposalDetails $detail): self
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getProposal() === $this) {
                $detail->setProposal(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

}
