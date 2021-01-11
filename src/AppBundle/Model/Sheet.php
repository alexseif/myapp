<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

/**
 * Description of Sheet
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Sheet
{

    /**
     *
     * @var string title
     */
    private $title;

    /**
     *
     * @var \DateTime date
     */
    private $date;

    /**
     *
     * @var \DateInterval days
     */
    private $days;

    /**
     *
     * @var int amount
     */
    private $amount;

    /**
     *
     * @var int balance
     */
    private $balance;

    public function __construct($title, \DateTime $date, \DateInterval $days, $amount, $balance)
    {
        $this->setTitle($title);
        $this->setDate($date);
        $this->setDays($days);
        $this->setAmount($amount);
        $this->setBalance($balance);
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     *
     * @return \DateInterval
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     *
     * @param \DateInterval $days
     */
    public function setDays(\DateInterval $days)
    {
        $this->days = $days;
    }

    /**
     *
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     *
     * @param int $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

}
