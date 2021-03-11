<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ProposalDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProposalDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProposalDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProposalDetails[]    findAll()
 * @method ProposalDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProposalDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProposalDetails::class);
    }

    // /**
    //  * @return ProposalDetails[] Returns an array of ProposalDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProposalDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
