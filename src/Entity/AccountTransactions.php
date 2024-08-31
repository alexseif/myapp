<?php

namespace App\Entity;

use App\Repository\AccountTransactionsRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * AccountTransactions.
 *
 * @ORM\Table(name="account_transactions")
 * @ORM\Entity(repositoryClass=AccountTransactionsRepository::class)
 */
class AccountTransactions
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
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="issuedAt", type="date")
     */
    private $issuedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="transactions")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    /**
     * AccountTransactions constructor.
     */
    public function __construct()
    {
        $this->issuedAt = new DateTime();
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
     * Set amount.
     *
     * @param int $amount
     *
     * @return AccountTransactions
     */
    public function setAmount($amount): AccountTransactions
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Set note.
     *
     * @param string $note
     *
     * @return AccountTransactions
     */
    public function setNote($note): AccountTransactions
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * Set account.
     *
     * @param Accounts $account
     *
     * @return AccountTransactions
     */
    public function setAccount(Accounts $account = null): AccountTransactions
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account.
     *
     * @return Accounts
     */
    public function getAccount(): Accounts
    {
        return $this->account;
    }

    /**
     * Set issuedAt.
     *
     * @param DateTime $issuedAt
     *
     * @return AccountTransactions
     */
    public function setIssuedAt($issuedAt): AccountTransactions
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt.
     *
     * @return DateTime
     */
    public function getIssuedAt(): DateTime
    {
        return $this->issuedAt;
    }
}
