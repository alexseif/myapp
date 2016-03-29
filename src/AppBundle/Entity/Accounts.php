<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Accounts
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountsRepository")
 */
class Accounts
{

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
     * @ORM\OneToMany(targetEntity="AccountBalances", mappedBy="account")
     */
    private $balances;

    /**
     * @ORM\OneToMany(targetEntity="AccountPayments", mappedBy="account")
     */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="TaskLists", mappedBy="account")
     */
    private $taskLists;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Accounts
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
     * Constructor
     */
    public function __construct()
    {
        $this->balances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add balances
     *
     * @param \AppBundle\Entity\AccountBalances $balances
     * @return Accounts
     */
    public function addBalance(\AppBundle\Entity\AccountBalances $balances)
    {
        $balances->setAccount($this);
        $this->balances[] = $balances;

        return $this;
    }

    /**
     * Remove balances
     *
     * @param \AppBundle\Entity\AccountBalances $balances
     */
    public function removeBalance(\AppBundle\Entity\AccountBalances $balances)
    {
        $this->balances->removeElement($balances);
    }

    /**
     * Get balances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBalances()
    {
        return $this->balances;
    }

    /**
     * Add payments
     *
     * @param \AppBundle\Entity\AccountPayments $payments
     * @return Accounts
     */
    public function addPayment(\AppBundle\Entity\AccountPayments $payments)
    {
        $payments->setAccount($this);
        $this->payments[] = $payments;
        return $this;
    }

    /**
     * Remove payments
     *
     * @param \AppBundle\Entity\AccountPayments $payments
     */
    public function removePayment(\AppBundle\Entity\AccountPayments $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

}
