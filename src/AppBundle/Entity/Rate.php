<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rate
 *
 * @ORM\Table(name="rate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RateRepository")
 */
class Rate
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
   * @var float
   *
   * @ORM\Column(name="rate", type="float")
   */
  private $rate;

  /**
   * @ORM\ManyToOne(targetEntity="Client", inversedBy="rates")
   * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
   */
  private $client;

  /**
   * @var bool
   *
   * @ORM\Column(name="active", type="boolean")
   */
  private $active;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  function __construct()
  {
    $this->createdAt = new \DateTime();
  }

  /**
   * Get id.
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set rate.
   *
   * @param float $rate
   *
   * @return Rate
   */
  public function setRate($rate)
  {
    $this->rate = $rate;

    return $this;
  }

  /**
   * Get rate.
   *
   * @return float
   */
  public function getRate()
  {
    return $this->rate;
  }

  /**
   * Set createdAt.
   *
   * @param \DateTime $createdAt
   *
   * @return Rate
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt.
   *
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Set client.
   *
   * @param \AppBundle\Entity\Client|null $client
   *
   * @return Rate
   */
  public function setClient(\AppBundle\Entity\Client $client = null)
  {
    $this->client = $client;

    return $this;
  }

  /**
   * Get client.
   *
   * @return \AppBundle\Entity\Client|null
   */
  public function getClient()
  {
    return $this->client;
  }

  /**
   * Set active.
   *
   * @param bool $active
   *
   * @return Rate
   */
  public function setActive($active)
  {
    $this->active = $active;

    return $this;
  }

  /**
   * Get active.
   *
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }

}
