<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cost
 *
 * @ORM\Table(name="cost")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CostRepository")
 */
class Cost
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
   * @var string
   *
   * @ORM\Column(name="note", type="text", nullable=true)
   */
  private $note;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="generatedAt", type="datetime")
   */
  private $generatedAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  public function __construct()
  {
    $this->generatedAt = $this->createdAt = new \DateTime();
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
   * @return Cost
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
   * @return Cost
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
   * @return Cost
   */
  public function setCurrency($currency)
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
   * Set generatedAt
   *
   * @param \DateTime $generatedAt
   *
   * @return Cost
   */
  public function setGeneratedAt($generatedAt)
  {
    $this->generatedAt = $generatedAt;

    return $this;
  }

  /**
   * Get generatedAt
   *
   * @return \DateTime
   */
  public function getGeneratedAt()
  {
    return $this->generatedAt;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   *
   * @return Cost
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
     * Set note
     *
     * @param string $note
     *
     * @return Cost
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
}
