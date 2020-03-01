<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Description of Contract Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class Contract
{

  /**
   * 
   * @var EntityManager $em
   */
  protected $em;

  /**
   * 
   * @var CostService $cs
   */
  protected $cs;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

}
