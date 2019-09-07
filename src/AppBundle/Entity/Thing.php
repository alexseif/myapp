<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thing
 *
 * @ORM\Table(name="thing")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThingRepository")
 */
class Thing
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
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

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
   * @return Thing
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
   * Set createdAt.
   *
   * @param \DateTime $createdAt
   *
   * @return Thing
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

}
