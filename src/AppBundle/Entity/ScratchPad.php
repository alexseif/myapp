<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScratchPad
 *
 * @ORM\Table(name="scratch_pad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScratchPadRepository")
 */
class ScratchPad
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
   * @var array
   *
   * @ORM\Column(name="content", type="text")
   */
  private $content;

  /**
   * @var datetime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->createdAt = new \DateTime();
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
   * Set content
   *
   * @param array $content
   *
   * @return ScratchPad
   */
  public function setContent($content)
  {
    $this->content = $content;

    return $this;
  }

  /**
   * Get content
   *
   * @return array
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return ScratchPad
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

}
