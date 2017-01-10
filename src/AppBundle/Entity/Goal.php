<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Goal
 *
 * @ORM\Table(name="goal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GoalRepository")
 */
class Goal
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
     * @ORM\Column(name="goalOrder", type="integer")
     */
    private $goalOrder = 0;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="requirements", type="object", nullable=true)
     */
    private $requirements;


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
     * @return Goal
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
     * Set goalOrder
     *
     * @param integer $goalOrder
     * @return Goal
     */
    public function setGoalOrder($goalOrder)
    {
        $this->goalOrder = $goalOrder;

        return $this;
    }

    /**
     * Get goalOrder
     *
     * @return integer 
     */
    public function getGoalOrder()
    {
        return $this->goalOrder;
    }

    /**
     * Set requirements
     *
     * @param \stdClass $requirements
     * @return Goal
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    /**
     * Get requirements
     *
     * @return \stdClass 
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}
