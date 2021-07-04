<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScenarioDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScenarioDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScenarioDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScenarioDetails[]    findAll()
 * @method ScenarioDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScenarioDetails::class);
    }

    public function getToday()
    {
        return $this->createQueryBuilder('sd')
            ->where('sd.date = date(now())')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ScenarioDetails[] Returns an array of ScenarioDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScenarioDetails
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
