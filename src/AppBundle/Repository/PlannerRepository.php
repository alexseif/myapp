<?php

namespace AppBundle\Repository;

/**
 * PlannerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlannerRepository extends \Doctrine\ORM\EntityRepository
{

  public function findPlannerByTask(\AppBundle\Entity\Tasks $task)
  {
    $qb = $this->createQueryBuilder("p")
        ->where(':task MEMBER OF p.tasks')
        ->setParameters(array('task' => $task))
    ;
    return $qb->getQuery()->getOneOrNullResult();
  }

}
