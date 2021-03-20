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
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add account
     *
     * @param Accounts $account
     *
     * @return Client
     */
    public function addAccount(Accounts $account)
    {
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account
     *
     * @param Accounts $account
     */
    public function removeAccount(Accounts $account)
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

    public function __toString()
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
    public function addRate(Rate $rate)
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
    public function removeRate(Rate $rate)
    {
        return $this->rates->removeElement($rate);
    }

    /**
     * Get rates.
     *
     * @return Collection
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Check rates
     *
     * @return int
     */
    public function hasRates()
    {
        return $this->getRates()->count();
    }

    public function getRate()
    {
        return ($this->getRates()->count()) ? $this->getRates()->last()->getRate() : null;
    }

    function getContracts()
    {
        return $this->contracts;
    }

    function setContracts($contracts)
    {
        $this->contracts = $contracts;
    }

}
