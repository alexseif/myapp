<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * CostOfLife
 *
 * @ORM\Table(name="cost_of_life")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CostOfLifeRepository")
 */
class CostOfLife
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
   * @var float
   *
   * @ORM\Column(name="value", type="float")
   */
  private $value;

  /**
   * @ORM\ManyToOne(targetEntity="Currency")
   * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
   */
  private $currency;

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
   * @return CostOfLife
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
   * Set value
   *
   * @param float $value
   *
   * @return CostOfLife
   */
  public function setValue($value)
  {
    $this->value = $value;

    return $this;
  }

  /**
   * Get value
   *
   * @return float
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

}
