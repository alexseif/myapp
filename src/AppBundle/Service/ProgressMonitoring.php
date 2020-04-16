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
class ProgressMonitoring
{

  /**
   * 
   * @var EntityManager $em
   */
  protected $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function getClientsCount()
  {
    $clients = $this->em->getRepository('AppBundle:Client')->findAll();
    return count($clients);
  }

  public function getTasksCount()
  {
    $tasks = $this->em->getRepository('AppBundle:Tasks')->findAll();
    return count($tasks);
  }

  public function getRevenueSum()
  {
    $revenue = $this->em->getRepository('AppBundle:AccountTransactions')->getRevenueSum();
    return array_pop($revenue);
  }

}
