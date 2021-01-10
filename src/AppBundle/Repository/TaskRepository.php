<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findAll()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t', 'i')
            ->leftJoin('t.item', 'i')
            ->getQuery()
            ->getResult();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $tasksQueryBuilder = $this
            ->createQueryBuilder('t')
            ->select('t', 'i')
            ->leftJoin('t.item', 'i');

        foreach ($criteria as $column => $value)
            $tasksQueryBuilder->where($column, $value);
        if ($orderBy != null)
            $tasksQueryBuilder->add('orderBy', "{$orderBy[0]} {$orderBy[1]}");
        return $tasksQueryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery(Query::HYDRATE_OBJECT)
            ->getResult();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
