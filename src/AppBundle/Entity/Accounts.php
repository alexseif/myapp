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
   * @ORM\OneToMany(targetEntity="AccountTransactions", mappedBy="account", cascade="remove")
   */
  private $transactions;

  /**
   * Many Accounts have Many Costs.
   * @ORM\ManyToMany(targetEntity="Cost", cascade={"persist"})
   * @ORM\JoinTable(name="account_costs",
   *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="cost_id", referencedColumnName="id", unique=true)}
   *      )
   */
  private $costs;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    $this->costs = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Add transactions
   *
   * @param \AppBundle\Entity\AccountTransactions $transactions
   * @return Accounts
   */
  public function addTransaction(\AppBundle\Entity\AccountTransactions $transactions)
  {
    $this->transactions[] = $transactions;

    return $this;
  }

  /**
   * Remove transactions
   *
   * @param \AppBundle\Entity\AccountTransactions $transactions
   */
  public function removeTransaction(\AppBundle\Entity\AccountTransactions $transactions)
  {
    $this->transactions->removeElement($transactions);
  }

  /**
   * Get transactions
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getTransactions()
  {
    return $this->transactions;
  }

  public function getBalance()
  {
    $balance = 0;
    $transactions = $this->getTransactions();
    foreach ($transactions as $transaction) {
      $balance += $transaction->getAmount();
    }
    return $balance;
  }

  /**
   * Add cost
   *
   * @param \AppBundle\Entity\Cost $cost
   *
   * @return Accounts
   */
  public function addCost(\AppBundle\Entity\Cost $cost)
  {
    $this->costs[] = $cost;

    return $this;
  }

  /**
   * Remove cost
   *
   * @param \AppBundle\Entity\Cost $cost
   */
  public function removeCost(\AppBundle\Entity\Cost $cost)
  {
    $this->costs->removeElement($cost);
  }

  /**
   * Get costs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getCosts()
  {
    return $this->costs;
  }

}
