<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Routine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Routine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Routine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Routine[]    findAll()
 * @method Routine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineRepository extends ServiceEntityRepository
{
    /**
     * RoutineRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Routine::class);
    }

    /**
     * @param $dayOfWeek
     * @param false $timeOfDay
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByDayAndTime($dayOfWeek, $timeOfDay = false)
    {
        $qb = $this->createQueryBuilder('r');

        if ($timeOfDay !== false) {
            $qb
                ->where(':timeOfDay >= r.timeOfDay')
                ->orWhere('r.timeOfDay IS NULL')
                ->setParameter(':timeOfDay', $timeOfDay);
        }


        return $qb
            ->andWhere('r.daysOfWeek LIKE :dayOfWeek')
            ->setParameter(':dayOfWeek', "%" . $dayOfWeek . "%")
            ->orderBy('r.priority')
            ->addOrderBy('r.sort')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Routine[] Returns an array of Routine objects
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
    public function findOneBySomeField($value): ?Routine
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
