<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountTransactions
 *
 * @ORM\Table(name="account_transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountTransactionsRepository")
 */
class AccountTransactions
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
   * @var \DateTime
   *
   * @ORM\Column(name="issuedAt", type="date")
   */
  private $issuedAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="transactions")
   * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
   */
  private $account;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->issuedAt = $this->createdAt = new \DateTime();
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set amount
   *
   * @param integer $amount
   * @return AccountTransactions
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;

    return $this;
  }

  /**
   * Get amount
   *
   * @return integer 
   */
  public function getAmount()
  {
    return $this->amount;
  }

  /**
   * Set note
   *
   * @param string $note
   * @return AccountTransactions
   */
  public function setNote($note)
  {
    $this->note = $note;

    return $this;
  }

  /**
   * Get note
   *
   * @return string 
   */
  public function getNote()
  {
    return $this->note;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return AccountTransactions
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

  /**
   * Set account
   *
   * @param \AppBundle\Entity\Accounts $account
   * @return AccountTransactions
   */
  public function setAccount(\AppBundle\Entity\Accounts $account = null)
  {
    $this->account = $account;

    return $this;
  }

  /**
   * Get account
   *
   * @return \AppBundle\Entity\Accounts 
   */
  public function getAccount()
  {
    return $this->account;
  }

  /**
   * Set issuedAt
   *
   * @param \DateTime $issuedAt
   * @return AccountTransactions
   */
  public function setIssuedAt($issuedAt)
  {
    $this->issuedAt = $issuedAt;

    return $this;
  }

  /**
   * Get issuedAt
   *
   * @return \DateTime 
   */
  public function getIssuedAt()
  {
    return $this->issuedAt;
  }

}
