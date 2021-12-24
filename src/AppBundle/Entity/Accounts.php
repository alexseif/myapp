<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Accounts.
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountsRepository")
 */
class Accounts
{
    use TimestampableEntity;

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
     * @var bool
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
     * Constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->taskLists = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Accounts
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add transactions.
     *
     * @return Accounts
     */
    public function addTransaction(AccountTransactions $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions.
     */
    public function removeTransaction(AccountTransactions $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions.
     *
     * @return Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    public function getBalance()
    {
        $balance = 0;
        foreach ($this->getTransactions() as $transaction) {
            $balance += $transaction->getAmount();
        }

        return $balance;
    }

    /**
     * Set client.
     *
     * @param Client $client
     *
     * @return Accounts
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add taskList.
     *
     * @return Accounts
     */
    public function addTaskList(TaskLists $taskList)
    {
        $this->taskList[] = $taskList;

        return $this;
    }

    /**
     * Remove taskList.
     */
    public function removeTaskList(TaskLists $taskList)
    {
        $this->taskList->removeElement($taskList);
    }

    /**
     * Get taskLists.
     *
     * @return Collection
     */
    public function getTaskLists()
    {
        return $this->taskLists;
    }

    /**
     * Set conceal.
     *
     * @param bool $conceal
     *
     * @return Accounts
     */
    public function setConceal($conceal)
    {
        $this->conceal = $conceal;

        return $this;
    }

    /**
     * Get conceal.
     *
     * @return bool
     */
    public function getConceal()
    {
        return $this->conceal;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
