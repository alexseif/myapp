<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Focus
 *
 * @ORM\Table(name="focus")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FocusRepository")
 */
class Focus
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
   * @ORM\ManyToOne(targetEntity="TaskLists")
   * @ORM\JoinColumn(name="task_list_id", referencedColumnName="id")
   */
  private $taskList;

  /**
   * @var integer
   *
   * @ORM\Column(name="duration", type="integer", nullable=true)
   */
  private $duration;

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
   * @return Focus
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
   * Set duration.
   *
   * @param string $duration
   *
   * @return Focus
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;

    return $this;
  }

  /**
   * Get duration.
   *
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * Set taskList.
   *
   * @param \AppBundle\Entity\TaskLists|null $taskList
   *
   * @return Focus
   */
  public function setTaskList(\AppBundle\Entity\TaskLists $taskList = null)
  {
    $this->taskList = $taskList;

    return $this;
  }

  /**
   * Get taskList.
   *
   * @return \AppBundle\Entity\TaskLists|null
   */
  public function getTaskList()
  {
    return $this->taskList;
  }

}
