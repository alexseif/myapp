<?php

namespace App\Entity;

use App\Repository\AccountsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Accounts.
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass=AccountsRepository::class)
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\OneToMany(targetEntity="AccountTransactions", mappedBy="account",
     *   cascade={"remove"})
     */
    private $transactions;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="accounts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id",
     *   nullable=true)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="TaskLists", mappedBy="account")
     */
    private $taskLists;

    /**
     * @var int
     */
    private $balance = 0;

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
    public function getId(): int
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
    public function setName($name): Accounts
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Add transactions.
     *
     * @return Accounts
     */
    public function addTransaction(AccountTransactions $transactions): Accounts
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions.
     */
    public function removeTransaction(AccountTransactions $transactions): void
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

    /**
     * @ORM\PostLoad()
     */
    public function calculateBalance(): void
    {
        $balance = 0;
        /** @var AccountTransactions $transaction */
        foreach ($this->getTransactions() as $transaction) {
            $balance += $transaction->getAmount();
        }

        $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }


    /**
     * Set client.
     *
     */
    public function setClient(Client $client = null): Accounts
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Add taskLists.
     *
     * @return Accounts
     */
    public function addTaskList(TaskLists $taskList): Accounts
    {
        $this->taskLists[] = $taskList;

        return $this;
    }

    /**
     * Remove taskLists.
     */
    public function removeTaskList(TaskLists $taskList): void
    {
        $this->taskLists->removeElement($taskList);
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
    public function setConceal($conceal): Accounts
    {
        $this->conceal = $conceal;

        return $this;
    }

    /**
     * Get conceal.
     *
     * @return bool
     */
    public function getConceal(): bool
    {
        return $this->conceal;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
