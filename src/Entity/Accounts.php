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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="conceal", type="boolean")
     */
    private bool $conceal = false;

    /**
     * @ORM\OneToMany(targetEntity="AccountTransactions", mappedBy="account",
     *   cascade={"remove"})
     */
    private Collection $transactions;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="accounts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id",
     *   nullable=true)
     */
    private Client $client;

    /**
     * @ORM\OneToMany(targetEntity="TaskLists", mappedBy="account")
     */
    private Collection $taskLists;

    /**
     * @var int
     */
    private int $balance = 0;

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
    public function setName(string $name): Accounts
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
    public function setConceal(bool $conceal): Accounts
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
