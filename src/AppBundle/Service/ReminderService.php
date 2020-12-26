<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Days;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DaysService
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ReminderService
{

  protected $em;

  function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function getEm()
  {
    return $this->em;
  }

  /**
   * 
   * @return \AppBundle\Repository\DaysRepository
   */
  public function getRepository()
  {
    return $this->getEm()->getRepository('AppBundle:Days');
  }

  public function getActiveReminders()
  {
    return $this->getRepository()->getActiveCards();
  }

}
