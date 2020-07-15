<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AccountsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountsRepository extends EntityRepository
{

  public function search($searchTerm)
  {
    return $this
            ->createQueryBuilder('a')
            ->select()
            ->where('a.name LIKE :searchTerm')
            ->setParameter(":searchTerm", '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
  }

  public function findAllwithJoin()
  {

    return $this
            ->createQueryBuilder('a')
            ->select('a, c')
            ->leftJoin('a.client', 'c');
  }

  /**
   *
   * @return array The objects.
   */
  public function findByYearAndClient($year, $client)
  {
    $queryBuilder = $this
        ->createQueryBuilder('a')
        ->select('t, tl, a, c')
        ->leftJoin('a.taskLists', 'tl')
        ->leftJoin('a.client', 'c')
        ->leftJoin('tl.tasks', 't')
        ->where('YEAR(t.completedAt) = :year')
        ->andWhere('a.client = :client')
        ->setParameter(':year', $year)
        ->setParameter(':client', $client)
        ->groupBy('tl.account')
    ;

    return $queryBuilder
            ->getQuery()
            ->getResult();
  }

  public function getCreatedTillYear($year)
  {
    return $this
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->where('YEAR(a.createdAt) <= :year')
            ->setParameter(":year", $year)
            ->getQuery()
            ->getSingleScalarResult();
  }

}
