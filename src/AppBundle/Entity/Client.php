<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client
{

    use TimestampableEntity;
    use Traits\Status;

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
     * @ORM\OneToMany(targetEntity="Accounts", mappedBy="client")
     */
    private $accounts;

    /**
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="client")
     */
    private $contracts;

    /**
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="client", cascade="remove")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $rates;

    /**
     * @ORM\OneToMany(targetEntity=Proposal::class, mappedBy="client")
     */
    private $proposals;


    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $billingOption = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->proposals = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Client
     */
    public function setName(string $name): Client
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: '';
    }

    /**
     * Add account
     *
     * @param Accounts $account
     *
     * @return Client
     */
    public function addAccount(Accounts $account): Client
    {
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account
     *
     * @param Accounts $account
     */
    public function removeAccount(Accounts $account): void
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts
     *
     * @return Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Add rate.
     *
     * @param Rate $rate
     *
     * @return Client
     */
    public function addRate(Rate $rate): Client
    {
        $this->rates[] = $rate;

        return $this;
    }

    /**
     * Remove rate.
     *
     * @param Rate $rate
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRate(Rate $rate): bool
    {
        return $this->rates->removeElement($rate);
    }

    /**
     * Get rates.
     *
     * @return Collection
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    /**
     * Check rates
     *
     * @return int
     */
    public function hasRates(): int
    {
        return $this->getRates()->count();
    }

    public function getRate()
    {
        return ($this->getRates()->count()) ? $this->getRates()->last()->getRate() : null;
    }

    public function getContracts()
    {
        return $this->contracts;
    }

    public function setContracts($contracts): void
    {
        $this->contracts = $contracts;
    }

    /**
     * @return Collection|Proposal[]
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setClient($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        // set the owning side to null (unless already changed)
        if ($this->proposals->removeElement($proposal) && $proposal->getClient() === $this) {
            $proposal->setClient(null);
        }

        return $this;
    }

    public function getBillingOption(): ?array
    {
        return $this->billingOption;
    }

    public function setBillingOption(?array $billingOption): self
    {
        $this->billingOption = $billingOption;

        return $this;
    }
}
