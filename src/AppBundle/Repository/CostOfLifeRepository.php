<?php

namespace AppBundle\Repository;

/**
 * CostOfLifeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CostOfLifeRepository extends \Doctrine\ORM\EntityRepository
{

  public function sumCostOfLife()
  {
    return $this
            ->createQueryBuilder('col')
            ->select('SUM(col.value)/100 as cost')
            ->getQuery()
            ->getSingleResult();
  }

}
