<?php

namespace App\Repository;

use App\Entity\WorkLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * WorkLogRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkLog::class);
    }

    public function orderByTaskList()
    {
        return $this
          ->createQueryBuilder('wl')
          ->select('wl, t')
          ->join('wl.task', 't')
          ->orderBy('t.taskList')
          ->addOrderBy('wl.createdAt', 'DESC')
          ->getQuery()
          ->getResult();
    }

    public function getByTaskList($taskList)
    {
        return $this
          ->createQueryBuilder('wl')
          ->select('wl, t')
          ->join('wl.task', 't')
          ->where('t.taskList = :tasklist')
          ->setParameter(':tasklist', $taskList)
          ->addOrderBy('wl.createdAt', 'DESC')
          ->getQuery()
          ->getResult();
    }

}
