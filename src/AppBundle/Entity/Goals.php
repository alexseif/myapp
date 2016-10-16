<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goals
 *
 * @ORM\Table(name="goals")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GoalsRepository")
 */
class Goals
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
   * @ORM\OneToMany(targetEntity="Tasks", mappedBy="goal", cascade="remove")
   */
  private $tasks;

  /**
   * @ORM\OneToMany(targetEntity="Obstacles", mappedBy="goal", cascade="remove")
   */
  private $obstacles;

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
   * @return Goals
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
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->obstacles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tasks
     *
     * @param \AppBundle\Entity\Tasks $tasks
     * @return Goals
     */
    public function addTask(\AppBundle\Entity\Tasks $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \AppBundle\Entity\Tasks $tasks
     */
    public function removeTask(\AppBundle\Entity\Tasks $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add obstacles
     *
     * @param \AppBundle\Entity\Obstacles $obstacles
     * @return Goals
     */
    public function addObstacle(\AppBundle\Entity\Obstacles $obstacles)
    {
        $this->obstacles[] = $obstacles;

        return $this;
    }

    /**
     * Remove obstacles
     *
     * @param \AppBundle\Entity\Obstacles $obstacles
     */
    public function removeObstacle(\AppBundle\Entity\Obstacles $obstacles)
    {
        $this->obstacles->removeElement($obstacles);
    }

    /**
     * Get obstacles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObstacles()
    {
        return $this->obstacles;
    }
}
