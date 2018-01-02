<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CurrencyRepository")
 */
class Currency
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
   * @ORM\Column(name="code", type="string", length=3, unique=true)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var float
   *
   * @ORM\Column(name="egp", type="float")
   */
  private $EGP;

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
   * Set code
   *
   * @param string $code
   *
   * @return Currency
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return Currency
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

  public function __toString()
  {
    return $this->code;
  }

}
