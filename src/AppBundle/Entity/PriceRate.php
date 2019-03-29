<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PriceRate
 *
 * @ORM\Table(name="price_rate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PriceRateRepository")
 */
class PriceRate
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
   * @var string
   *
   * @ORM\Column(name="unit", type="string", length=255)
   */
  private $unit;

  /**
   * @var float
   *
   * @ORM\Column(name="pricePerUnit", type="float")
   */
  private $pricePerUnit;

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
   * Set unit
   *
   * @param string $unit
   *
   * @return PriceRate
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;

    return $this;
  }

  /**
   * Get unit
   *
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }

  /**
   * Set pricePerUnit
   *
   * @param float $pricePerUnit
   *
   * @return PriceRate
   */
  public function setPricePerUnit($pricePerUnit)
  {
    $this->pricePerUnit = $pricePerUnit;

    return $this;
  }

  /**
   * Get pricePerUnit
   *
   * @return float
   */
  public function getPricePerUnit()
  {
    return $this->pricePerUnit;
  }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return PriceRate
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
}
