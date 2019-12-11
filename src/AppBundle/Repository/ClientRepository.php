<?php

namespace AppBundle\Repository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends \Doctrine\ORM\EntityRepository
{

  public function search($searchTerm)
  {
    return $this
            ->createQueryBuilder('c')
            ->select()
            ->where('c.name LIKE :searchTerm')
            ->setParameter(":searchTerm", '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
  }

  /**
   *
   * @return array The objects.
   */
  public function findByYear($year)
  {
    $queryBuilder = $this
        ->createQueryBuilder('c')
        ->select('t, tl, a, c')
        ->leftJoin('c.accounts', 'a')
        ->leftJoin('a.taskLists', 'tl')
        ->leftJoin('tl.tasks', 't')
        ->where('YEAR(t.completedAt) = :year')
        ->setParameter(':year', $year)
        ->groupBy('c.id')
    ;
    return $queryBuilder
            ->getQuery()
            ->getResult();
  }

}
