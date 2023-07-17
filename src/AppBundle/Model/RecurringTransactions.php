<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }
}
