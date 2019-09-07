<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Description of Bottom Bar Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class BottomBar
{

  protected $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function getThings()
  {
    return $this->em->getRepository('AppBundle:Thing')->findBy([], ['createdAt' => 'asc'], 5);
  }

}
