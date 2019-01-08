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

  public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
  {
    if (is_null($orderBy)) {
      $orderBy = ["completed" => "ASC", "order" => "ASC"];
    }
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

  public function getIncomplete()
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

  public function getIncopmleteTasks()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
  }

  public function getCompletedToday()
  {
    $today = new \DateTime();
//    $today->sub(new \DateInterval("P1D"));
    $today->setTime(00, 00, 00);
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completedAt > :today')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.completedAt", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
  }

  public function focusList()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
  }

  public function focusLimitList()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setMaxResults(30)
            ->setParameter(':today', $today->format('Y-m-d H:i'))
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

  public function countByUrgenctAndPriority()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.id) as cnt, SUM(t.est) as est, t.urgency, t.priority')
            ->where('t.completed <> true')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->groupBy('t.urgency')
            ->addGroupBy('t.priority')
            ->getQuery()
            ->getResult();
  }

  public function sumByUrgenctAndPriority()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.est), t.urgency, t.priority')
            ->where('t.completed <> true')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->groupBy('t.urgency')
            ->addGroupBy('t.priority')
            ->getQuery()
            ->getResult();
  }

  public function randomTasks()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->addSelect('RAND() as HIDDEN rand')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->addOrderBy("rand", "ASC")
            ->setMaxResults(5)
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
  }

  public function singleTaskList()
  {
    $today = new \DateTime();
    return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.taskList", "DESC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
  }

  public function weightedList()
  {
    return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.urgency) as cnt, t.urgency, t.priority, tl.id')
            ->where('t.completed <> true')
            ->leftJoin('t.taskList', 'tl')
            ->groupBy('t.urgency, t.priority, tl.id')
            ->orderBy('t.urgency', 'DESC')
            ->addOrderBy('t.priority', 'DESC')
            ->addOrderBy('cnt', 'DESC')
            ->getQuery()
            ->getResult();
  }

  public function search($searchTerm)
  {
    return $this
            ->createQueryBuilder('t')
            ->select()
            ->where('t.task LIKE :searchTerm')
            ->setParameter(":searchTerm", '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
  }

}
