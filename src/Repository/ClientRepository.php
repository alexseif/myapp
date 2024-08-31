<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ClientRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }
    public function search($searchTerm)
    {
        return $this
            ->createQueryBuilder('c')
            ->select()
            ->where('c.name LIKE :searchTerm')
            ->setParameter(':searchTerm', '%'.$searchTerm.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array the objects
     */
    public function findByYear($year): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->select('t, tl, a, c')
            ->leftJoin('c.accounts', 'a')
            ->leftJoin('a.taskLists', 'tl')
            ->leftJoin('tl.tasks', 't')
            ->where('YEAR(t.completedAt) = :year')
            ->setParameter(':year', $year)
            ->groupBy('c.id');

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getCreatedTillYear($year)
    {
        return $this
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('YEAR(c.createdAt) <= :year')
            ->andWhere('c.enabled = true')
            ->setParameter(':year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
