<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client
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
   * @ORM\OneToMany(targetEntity="Accounts", mappedBy="client")
   */
  private $accounts;

  /**
   * @ORM\OneToMany(targetEntity="Rate", mappedBy="client", cascade="remove")
   * @ORM\OrderBy({"createdAt" = "ASC"})
   */
  private $rates;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
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
   * @return Client
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
   * Add account
   *
   * @param \AppBundle\Entity\Accounts $account
   *
   * @return Client
   */
  public function addAccount(\AppBundle\Entity\Accounts $account)
  {
    $this->accounts[] = $account;

    return $this;
  }

  /**
   * Remove account
   *
   * @param \AppBundle\Entity\Accounts $account
   */
  public function removeAccount(\AppBundle\Entity\Accounts $account)
  {
    $this->accounts->removeElement($account);
  }

  /**
   * Get accounts
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAccounts()
  {
    return $this->accounts;
  }

  public function __toString()
  {
    return $this->getName();
  }

  /**
   * Add rate.
   *
   * @param \AppBundle\Entity\Rate $rate
   *
   * @return Client
   */
  public function addRate(\AppBundle\Entity\Rate $rate)
  {
    $this->rates[] = $rate;

    return $this;
  }

  /**
   * Remove rate.
   *
   * @param \AppBundle\Entity\Rate $rate
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeRate(\AppBundle\Entity\Rate $rate)
  {
    return $this->rates->removeElement($rate);
  }

  /**
   * Get rates.
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRates()
  {
    return $this->rates;
  }

  /**
   * Check rates 
   * 
   * @return int
   */
  public function hasRates()
  {
    return $this->getRates()->count();
  }

  public function getRate()
  {
    return ($this->getRates()->count()) ? $this->getRates()->last()->getRate() : null;
  }

}
