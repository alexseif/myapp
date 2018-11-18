<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

/**
 * Description of Recurring Transactions
 * Class assumes monthly recurrences for now
 *
 * @author Alex Seif <me@alexseif.com>
 */
class RecurringTransactions
{

  /**
   *
   * @var string title 
   */
  private $title;

  /**
   *
   * @var integer amount 
   */
  private $amount;

  /**
   *
   * @var \DateTime date
   */
  private $date;

  function __construct($title, $amount, \DateTime $date)
  {
    $this->title = $title;
    $this->amount = $amount;
    $this->date = $date;
  }

  /**
   * 
   * @return string
   */
  function getTitle()
  {
    return $this->title;
  }

  /**
   * 
   * @return integer
   */
  function getAmount()
  {
    return $this->amount;
  }

  /**
   * 
   * @return \DateTime 
   */
  function getDate()
  {
    return $this->date;
  }

  /**
   * 
   * @param string $title
   */
  function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * 
   * @param integer $amount
   */
  function setAmount($amount)
  {
    $this->amount = $amount;
  }

  /**
   * 
   * @param \DateTime $date
   */
  function setDate(\DateTime $date)
  {
    $this->date = $date;
  }

}
