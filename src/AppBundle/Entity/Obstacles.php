<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Obstacles
 *
 * @ORM\Table(name="obstacles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObstaclesRepository")
 */
class Obstacles
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
   * @var int
   *
   * @ORM\Column(name="priority", type="integer")
   */
  private $priority;

  /**
   * @var int
   *
   * @ORM\Column(name="urgency", type="integer")
   */
  private $urgency;

  /**
   * @var boolean
   *
   * @ORM\Column(name="completed", type="boolean")
   */
  private $completed;

  /**
   * @var datetime
   *
   * @ORM\Column(name="completedAt", type="datetime", nullable=true)
   */
  private $completedAt;

  /**
   * @var datetime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;
  
  /**
   * @ORM\ManyToOne(targetEntity="Goals", inversedBy="obstacles")
   * @ORM\JoinColumn(name="goal_id", referencedColumnName="id")
   */
  private $goal;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Obstacles
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
   * Set priority
   *
   * @param integer $priority
   * @return Obstacles
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;

    return $this;
  }

  /**
   * Get priority
   *
   * @return integer 
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * Set urgency
   *
   * @param integer $urgency
   * @return Obstacles
   */
  public function setUrgency($urgency)
  {
    $this->urgency = $urgency;

    return $this;
  }

  /**
   * Get urgency
   *
   * @return integer 
   */
  public function getUrgency()
  {
    return $this->urgency;
  }


    /**
     * Set completed
     *
     * @param boolean $completed
     * @return Obstacles
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean 
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     * @return Obstacles
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime 
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Obstacles
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
     * Set goal
     *
     * @param \AppBundle\Entity\Goals $goal
     * @return Obstacles
     */
    public function setGoal(\AppBundle\Entity\Goals $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * Get goal
     *
     * @return \AppBundle\Entity\Goals 
     */
    public function getGoal()
    {
        return $this->goal;
    }
}
