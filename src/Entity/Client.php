<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Client.
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    use TimestampableEntity;
    use \App\Entity\Traits\Status;

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
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="client", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $rates;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $billingOption = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): Client
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name ?: '';
    }

    /**
     * Add account.
     */
    public function addAccount(Accounts $account): Client
    {
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account.
     */
    public function removeAccount(Accounts $account): void
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts.
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
     */
    public function addRate(Rate $rate): Client
    {
        $this->rates[] = $rate;

        return $this;
    }

    /**
     * Remove rate.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeRate(Rate $rate): bool
    {
        return $this->rates->removeElement($rate);
    }

    /**
     * Get rates.
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    /**
     * Check rates.
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
