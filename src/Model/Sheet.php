<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Model;

use DateInterval;
use DateTime;

/**
 * Description of Sheet.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Sheet
{
    /**
     * @var string title
     */
    private $title;

    /**
     * @var DateTime date
     */
    private $date;

    /**
     * @var DateInterval days
     */
    private $days;

    /**
     * @var int amount
     */
    private $amount;

    /**
     * @var int balance
     */
    private $balance;

    public function __construct($title, DateTime $date, DateInterval $days, $amount, $balance)
    {
        $this->setTitle($title);
        $this->setDate($date);
        $this->setDays($days);
        $this->setAmount($amount);
        $this->setBalance($balance);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return DateInterval
     */
    public function getDays(): DateInterval
    {
        return $this->days;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function setDays(DateInterval $days): void
    {
        $this->days = $days;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param int $balance
     */
    public function setBalance($balance): void
    {
        $this->balance = $balance;
    }
}
