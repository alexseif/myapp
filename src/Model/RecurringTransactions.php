<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Model;

use DateTime;

/**
 * Description of Recurring Transactions
 * Class assumes monthly recurrences for now.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class RecurringTransactions
{

    /**
     * @var string title
     */
    private $title;

    /**
     * @var int amount
     */
    private $amount;

    /**
     * @var DateTime date
     */
    private $date;

    public function __construct($title, $amount, DateTime $date)
    {
        $this->setTitle($title);
        $this->setAmount($amount);
        $this->setDate($date);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

}
