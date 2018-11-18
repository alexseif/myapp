<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Balance
 *
 * @ORM\Table(name="balance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BalanceRepository")
 */
class Balance
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
   * @ORM\Column(name="title", type="string", length=255)
   */
  private $title;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="balanceAt", type="date")
   */
  private $balanceAt;

  /**
   * @var int
   *
   * @ORM\Column(name="amount", type="integer")
   */
  private $amount;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  function __construct()
  {
    $this->createdAt = new \DateTime();
    $this->balanceAt = new \DateTime();
  }

  /**
   * Get id
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set title
   *
   * @param string $title
   *
   * @return Balance
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set balanceAt
   *
   * @param \DateTime $balanceAt
   *
   * @return Balance
   */
  public function setBalanceAt($balanceAt)
  {
    $this->balanceAt = $balanceAt;

    return $this;
  }

  /**
   * Get balanceAt
   *
   * @return \DateTime
   */
  public function getBalanceAt()
  {
    return $this->balanceAt;
  }

  /**
   * Set amount
   *
   * @param integer $amount
   *
   * @return Balance
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;

    return $this;
  }

  /**
   * Get amount
   *
   * @return int
   */
  public function getAmount()
  {
    return $this->amount;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   *
   * @return Balance
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

}
