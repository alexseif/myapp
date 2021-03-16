<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TaskLists;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskLists|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskLists|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskLists[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskListsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskLists::class);
    }

    // /**
    //  * @return TaskLists[] Returns an array of TaskLists objects
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
    public function findOneBySomeField($value): ?TaskLists
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAll()
    {
        return $this
            ->createQueryBuilder('tl')
            ->select('tl, t, a, c, w')
            ->leftJoin('tl.tasks', 't')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'w')
            ->orderBy("tl.createdAt", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function findActive()
    {
        return $this->findActiveQuery()
            ->getQuery()
            ->getResult();
    }

    public function findActiveQuery()
    {
        return $this
            ->createQueryBuilder('tl')
            ->select('tl, a, c')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->where('tl.status <> \'archive\'')
            ->orderBy("tl.createdAt", "ASC");
    }

    public function findAllWithActiveTasks()
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('tl')
            ->select('tl, t')
            ->leftJoin('tl.tasks', 't')
            ->where('t.completedAt > :today')
            ->orWhere('t.completed <> true')
            ->addOrderBy("t.completed", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function search($searchTerm)
    {
        return $this
            ->createQueryBuilder('tl')
            ->select()
            ->where('tl.name LIKE :searchTerm')
            ->setParameter(":searchTerm", '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }

}
