<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TasksRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TasksRepository extends EntityRepository
{

  public function findBy(array $criteria, array $orderBy = array("completed" => "ASC", "order" => "ASC"), $limit = null, $offset = null)
  {
    return parent::findBy($criteria, $orderBy, $limit, $offset);
  }

  public function findAll()
  {
    return $this->findBy(array());
  }

  public function findUnListed()
  {
    return $this->findBy(array("taskList" => null));
  }

  public function getCompletedToday()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completedAt > :today')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.completedAt", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d'))
            ->getQuery()
            ->getResult();
  }

  public function focusList()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->getQuery()
            ->getResult();
  }

  public function focusByTasklist($taskList)
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->andWhere('t.taskList = :tasklist')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.completedAt", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':tasklist', $taskList)
            ->getQuery()
            ->getResult();
  }

  public function findTasksCountByDay()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.id) as cnt, DAYNAME(t.completedAt) as day_name')
            ->where('t.completed = 1')
            ->groupBy('day_name')
            ->orderBy('cnt', 'DESC')
            ->getQuery()
            ->getResult();
  }

  public function sumEst()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.est) as est')
            ->where('t.completed <> true')
            ->getQuery()
            ->getSingleResult();
  }
}
