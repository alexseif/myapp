<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountBalances
 *
 * @ORM\Table(name="account_balances")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountBalancesRepository")
 */
class AccountBalances
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
     * @var int
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="balances")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * Set balance
     *
     * @param integer $balance
     * @return AccountBalances
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AccountBalances
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set account
     *
     * @param \AppBundle\Entity\Accounts $account
     * @return AccountBalances
     */
    public function setAccount(\AppBundle\Entity\Accounts $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \AppBundle\Entity\Accounts 
     */
    public function getAccount()
    {
        return $this->account;
    }

}
