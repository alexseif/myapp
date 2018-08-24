<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account
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
   * One Account has One Node.
   * @ORM\OneToOne(targetEntity="Node", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
   */
  private $node;

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
   * Set node
   *
   * @param \AppBundle\Entity\Node $node
   *
   * @return Account
   */
  public function setNode(\AppBundle\Entity\Node $node)
  {
    $this->node = $node;

    return $this;
  }

  /**
   * Get node
   *
   * @return \AppBundle\Entity\Node
   */
  public function getNode()
  {
    return $this->node;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->node->getName();
  }

}
