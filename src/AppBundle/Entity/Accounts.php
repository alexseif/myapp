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
   * @var boolean
   *
   * @ORM\Column(name="conceal", type="boolean")
   */
  private $conceal = false;


  /**
   * @ORM\OneToMany(targetEntity="AccountTransactions", mappedBy="account", cascade="remove")
   */
  private $transactions;

  /**
   * @ORM\ManyToOne(targetEntity="Client", inversedBy="accounts")
   * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
   */
  private $client;

  /**
   * @ORM\OneToMany(targetEntity="TaskLists", mappedBy="account")
   */
  private $taskLists;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    $this->taskLists = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set client
   *
   * @param \AppBundle\Entity\Client $client
   *
   * @return Accounts
   */
  public function setClient(\AppBundle\Entity\Client $client = null)
  {
    $this->client = $client;

    return $this;
  }

  /**
   * Get client
   *
   * @return \AppBundle\Entity\Client
   */
  public function getClient()
  {
    return $this->client;
  }

  /**
   * Add taskList
   *
   * @param \AppBundle\Entity\TaskLists $taskList
   *
   * @return Accounts
   */
  public function addTaskList(\AppBundle\Entity\TaskLists $taskList)
  {
    $this->taskList[] = $taskList;

    return $this;
  }

  /**
   * Remove taskList
   *
   * @param \AppBundle\Entity\TaskLists $taskList
   */
  public function removeTaskList(\AppBundle\Entity\TaskLists $taskList)
  {
    $this->taskList->removeElement($taskList);
  }

  /**
   * Get taskLists
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaskLists()
  {
    return $this->taskLists;
  }


    /**
     * Set conceal
     *
     * @param boolean $conceal
     *
     * @return Accounts
     */
    public function setConceal($conceal)
    {
        $this->conceal = $conceal;

        return $this;
    }

    /**
     * Get conceal
     *
     * @return boolean
     */
    public function getConceal()
    {
        return $this->conceal;
    }
}
