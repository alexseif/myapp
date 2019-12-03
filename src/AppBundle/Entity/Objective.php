<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Objective
 *
 * @ORM\Table(name="objective")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObjectiveRepository")
 */
class Objective
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
   * @var string|null
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

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
   * @return Objective
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
   * Set description.
   *
   * @param string|null $description
   *
   * @return Objective
   */
  public function setDescription($description = null)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description.
   *
   * @return string|null
   */
  public function getDescription()
  {
    return $this->description;
  }

}
