<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Notes
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotesRepository")
 */
class Notes
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
   * @ORM\Column(name="note", type="text")
   */
  private $note;

  /**
   * @var string
   *
   * @ORM\Column(name="type", type="string", length=255)
   */
  private $type;

  function __construct()
  {
    $this->type = 'general';
  }

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
   * Set note
   *
   * @param string $note
   * @return Notes
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

  /**
   * Set type
   *
   * @param string $type
   * @return Notes
   */
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return string 
   */
  public function getType()
  {
    return $this->type;
  }

}
