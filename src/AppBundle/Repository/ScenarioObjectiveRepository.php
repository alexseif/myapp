<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScenarioObjective;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScenarioObjective|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScenarioObjective|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScenarioObjective[]    findAll()
 * @method ScenarioObjective[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioObjectiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScenarioObjective::class);
    }

    public function getToday()
    {
        return $this->createQueryBuilder('so')
            ->where('so.date = date(now())')
            ->orderBy('so.urgency')
            ->addOrderBy('so.priority')
            ->getQuery()
            ->getResult();
    }

    public function getAnObjective()
    {
        return $this->createQueryBuilder('so')
            ->where('so.date is null')
            ->orderBy('so.urgency')
            ->addOrderBy('so.priority')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return ScenarioObjective[] Returns an array of ScenarioObjective objects
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
    public function findOneBySomeField($value): ?ScenarioObjective
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
