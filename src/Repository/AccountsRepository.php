<?php

namespace App\Repository;

use App\Entity\Accounts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * AccountsRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accounts::class);
    }

    public function search($searchTerm)
    {
        return $this
          ->createQueryBuilder('a')
          ->select()
          ->where('a.name LIKE :searchTerm')
          ->setParameter(':searchTerm', '%' . $searchTerm . '%')
          ->getQuery()
          ->getResult();
    }

    public function findAllSorted()
    {
        return $this->findAllWithJoin()
          ->getQuery()
          ->getResult();
    }

    public function findAllWithJoin(): QueryBuilder
    {
        return $this
          ->createQueryBuilder('a')
          ->select('a, c')
          ->leftJoin('a.client', 'c')
          ->orderBy('c.enabled', 'DESC')
          ->addOrderBy('a.name');
    }

    /**
     * @param $year
     * @param $client
     *
     * @return array the objects
     */
    public function findByYearAndClient($year, $client): array
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
          ->groupBy('tl.account');

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
          ->setParameter(':year', $year)
          ->getQuery()
          ->getSingleScalarResult();
    }

    public function findByNotConceal()
    {
        return $this
          ->createQueryBuilder('a')
          ->select('a')
          ->addSelect('c')
          ->addSelect('at')
          ->leftJoin('a.client', 'c')
          ->leftJoin('a.transactions', 'at')
          ->where('a.conceal = false')
          ->groupBy('a.id')
          ->getQuery()
          ->getResult();
    }

    public function findWithBalance()
    {
        return $this
          ->createQueryBuilder('a')
          ->select('a')
          ->addSelect('c')
          ->addSelect('at')
          //          ->addSelect('SUM(at.amount) AS balance')
          ->leftJoin('a.client', 'c')
          ->leftJoin('a.transactions', 'at')
          ->where('a.conceal = false')
          ->groupBy('a.id')
          ->orderBy('c.enabled', 'DESC')
          ->addOrderBy('a.name')
          ->getQuery()
          ->getResult();
    }

}
