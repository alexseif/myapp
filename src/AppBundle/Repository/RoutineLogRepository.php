<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RoutineLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoutineLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoutineLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoutineLog[]    findAll()
 * @method RoutineLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoutineLog::class);
    }

    // /**
    //  * @return RoutineLog[] Returns an array of RoutineLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoutineLog
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
