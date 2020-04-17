<?php

namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Status Trait
 *
 * @author Alex Seif <alex.seif@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait Status
{

  /**
   * @var string
   *
   * @ORM\Column(name="status", type="string", length=255, nullable=true)
   */
  private $status;

  /**
   * @var bool
   *
   * @ORM\Column(name="enabled", type="boolean")
   */
  private $enabled = true;

  function getStatus()
  {
    return $this->status;
  }

  function setStatus($status)
  {
    $this->status = $status;
  }

  function getEnabled()
  {
    return $this->enabled;
  }

  function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }

}
