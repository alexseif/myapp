<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionsRepository")
 */
class Transactions
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
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date", type="date")
   */
  private $date;

  /**
   * @var integer
   *
   * @ORM\Column(name="value", type="integer")
   */
  private $value;

  /**
   * @ORM\ManyToOne(targetEntity="Currency")
   * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
   */
  private $currency;

  /**
   * @var float
   *
   * @ORM\Column(name="egp", type="float")
   */
  private $EGP;

  /**
   * @var datetime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  public function __construct()
  {
    $this->date = new \DateTime("NOW");
    $this->createdAt = new \DateTime();
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
   * Set name
   *
   * @param string $name
   *
   * @return transactions
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   *
   * @return transactions
   */
  public function setDate($date)
  {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * Set value
   *
   * @param string $value
   *
   * @return transactions
   */
  public function setValue($value)
  {
    $this->value = $value;

    return $this;
  }

  /**
   * Get value
   *
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Set currency
   *
   * @param integer $currency
   *
   * @return CostOfLife
   */
  public function setCurrency(\AppBundle\Entity\Currency $currency)
  {
    $this->currency = $currency;

    return $this;
  }

  /**
   * Get currency
   *
   * @return int
   */
  public function getCurrency()
  {
    return $this->currency;
  }

  /**
   * Set usd
   *
   * @param float $egp
   *
   * @return Currency
   */
  public function setEgp($egp)
  {
    $this->EGP = $egp;

    return $this;
  }

  /**
   * Get usd
   *
   * @return float
   */
  public function getEgp()
  {
    return $this->EGP;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return Tasks
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
