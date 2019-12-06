<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceRepository")
 * @Gedmo\Loggable
 */
class Service
{

  use TimestampableEntity;

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
   * @var int
   *
   * @ORM\Column(name="price", type="integer")
   */
  private $price;

  /**
   * @ORM\OneToOne(targetEntity="Currency")
   * @ORM\JoinColumn(name="currency", referencedColumnName="id")
   */
  protected $currency;

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
   * Set name.
   *
   * @param string $name
   *
   * @return Service
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set price.
   *
   * @param int $price
   *
   * @return Service
   */
  public function setPrice($price)
  {
    $this->price = $price;

    return $this;
  }

  /**
   * Get price.
   *
   * @return int
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * Set currency.
   *
   * @param string $currency
   *
   * @return Service
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;

    return $this;
  }

  /**
   * Get currency.
   *
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }

}
