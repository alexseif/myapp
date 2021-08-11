<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\Tasks;
use AppBundle\Util\DateRanges;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    // /**
    //  * @return Tasks[] Returns an array of Tasks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tasks
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

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
        $today = new DateTime();
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

    /**
     * List of completed tasks after date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getCompletedAfter(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('t.completedAt >= :date')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.completedAt", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count of completed tasks after date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getCompletedAfterCount(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('t.completedAt >= :date')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.completedAt", "ASC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * List of completed tasks today
     *
     * @return array The objects.
     */
    public function getCompletedToday()
    {
        $date = new DateTime();
        $date->setTime(00, 00, 00);

        return $this->getCompletedAfter($date);
    }

    /**
     * Count of completed tasks today
     *
     * @return array The objects.
     */
    public function getCompletedTodayCount()
    {
        $date = new DateTime();
        $date->setTime(00, 00, 00);

        return $this->getCompletedAfterCount($date);
    }

    /**
     * List of completed tasks this week
     *
     * @return array The objects.
     */
    public function getCompletedThisWeek()
    {
//    https://stackoverflow.com/a/11905818/1030170
        $day = date('w');
//    $week_start = date('Y-m-d H:i', strtotime('-' . $day . ' days'));
//    $week_end = date('Y-m-d H:i', strtotime('+' . (6 - $day) . ' days'));

        $date = new DateTime();
        $date->sub(new DateInterval("P" . $day . "D"));
        $date->setTime(00, 00, 00);
        return $this->getCompletedAfter($date);
    }

    /**
     * List of completed tasks this Month
     *
     * @return array The objects.
     */
    public function getCompletedThisMonth()
    {
        $date = DateRanges::getMonthStart();
        return $this->getCompletedAfter($date);
    }

    /**
     * Sums the duration of completed tasks after date
     *
     * @param DateTime $date
     * @return array The sum.
     */
    public function sumCompletedDurationAfter($date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('sum(t.duration) as duration')
            ->where('t.completedAt > :date')
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':date', $date->format('Y-m-d H:i'))
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * List of completed tasks today
     *
     * @return array The objects.
     */
    public function sumCompletedDurationToday()
    {
        $date = new DateTime();
        $date->setTime(00, 00, 00);

        return $this->sumCompletedDurationAfter($date);
    }

    /**
     * List of completed tasks this week
     *
     * @return array The objects.
     */
    public function sumCompletedDurationThisWeek()
    {
//    https://stackoverflow.com/a/11905818/1030170
        $day = date('w');
//    $week_start = date('Y-m-d H:i', strtotime('-' . $day . ' days'));
//    $week_end = date('Y-m-d H:i', strtotime('+' . (6 - $day) . ' days'));

        $date = new DateTime();
        $date->sub(new DateInterval("P" . $day . "D"));
        $date->setTime(00, 00, 00);
        return $this->sumCompletedDurationAfter($date);
    }

    /**
     * List of completed tasks this week
     *
     * @return array The objects.
     */
    public function sumCompletedDurationThisMonth()
    {
//    https://stackoverflow.com/a/11905818/1030170
//    $day = date('d');
//    $week_start = date('Y-m-d H:i', strtotime('-' . $day . ' days'));
//    $week_end = date('Y-m-d H:i', strtotime('+' . (6 - $day) . ' days'));

        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->setTime(00, 00, 00);
        return $this->sumCompletedDurationAfter($date);
    }

    /**
     *
     * @param int $limit
     * @return Tasks[]
     */
    public function focusList($limit = 20, $offset = 0)
    {
        $today = new DateTime();
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'wl')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->addOrderBy("tl.id", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'));

        $queryBuilder->andWhere('c.id <> 28');
        if ($limit > 0 && is_int($limit)) {
            $queryBuilder->setMaxResults($limit);
        }
        if ($offset > 0 && is_int($offset)) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function focusLimitList()
    {
        $today = new DateTime();
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
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'wl')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->andWhere('t.taskList = :tasklist')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->setParameter(':tasklist', $taskList)
            ->getQuery()
            ->getResult();
    }

    public function focusByClient($client)
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'wl')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->andWhere('a.client = :client')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':today', $today->format('Y-m-d H:i'))
            ->setParameter(':client', $client)
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

    public function countByUrgenctAndPriority()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.id) as cnt, SUM(t.duration) as duration, t.urgency, t.priority')
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
            ->select('SUM(t.duration), t.urgency, t.priority')
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
        $today = new DateTime();
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
        $today = new DateTime();
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
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.urgency) as cnt, t.urgency, t.priority, tl.id')
            ->where('t.completed <> true')
            ->andWhere('t.eta <= :today OR t.eta IS NULL')
            ->leftJoin('t.taskList', 'tl')
            ->groupBy('t.urgency, t.priority, tl.id')
            ->orderBy('t.urgency', 'DESC')
            ->addOrderBy('t.priority', 'DESC')
            ->addOrderBy('cnt', 'DESC')
            ->setParameter(':today', $today->format('Y-m-d H:i'))
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

    public function findCompletedByClient($client)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t.duration, MONTH(t.completedAt) as mnth, YEAR(t.completedAt) as yr')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt IS NOT NULL')
            ->setParameter(':client', $client)
            ->orderBy('t.completedAt')
            ->getQuery()
            ->getResult();
    }

    public function findDurationCompletedByClientByRange($client, $from, $to)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration) as duration')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt BETWEEN :from AND :to')
            ->setParameter(':client', $client)
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getSingleResult();
    }

    public function findCompletedByClientThisMonth($client)
    {
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->setTime(00, 00, 00);
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt > :date')
            ->setParameter(':client', $client)
            ->setParameter(':date', $date->format('Y-m-d H:i'))
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('date(t.completedAt) = :date')
            ->orderBy('t.completedAt')
            ->setParameter(':client', $client)
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientByRange($client, $from, $to)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt BETWEEN :from AND :to')
            ->orderBy('t.completedAt')
            ->setParameter(':client', $client)
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientByMonth($client, $month)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('YEAR(t.completedAt) = :year')
            ->andWhere('MONTH(t.completedAt) = :month')
            ->orderBy('t.completedAt')
            ->setParameter(':client', $client)
            ->setParameter(':month', $month->format('m'))
            ->setParameter(':year', $month->format('Y'))
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientToday($client)
    {
        $date = new DateTime();
        $date->setTime(00, 00, 00);
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration) as duration')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt > :date')
            ->setParameter(':client', $client)
            ->setParameter(':date', $date->format('Y-m-d H:i'))
            ->getQuery()
            ->getSingleResult();
    }

    public function sumDurationByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration) as duration')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->where('a.client = :client')
            ->andWhere('date(t.completedAt) = :date')
            ->setParameter(':client', $client)
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Finds Tasks entities with joins to increase performance
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return QueryBuilder
     */
    public function findByWithJoinsQuery(array $criteria, array $orderBy = [], $limit = null, $offset = null)
    {
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, w')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'w');

        $first = true;
        foreach ($criteria as $column => $value) {
            if ($first) {
                $queryBuilder->where("t.$column = :$column")
                    ->setParameter(":$column", $value);
                $first = false;
            } else {
                $queryBuilder->andWhere("t.$column = :$column")
                    ->setParameter(":$column", $value);
            }
        }
        $first = true;

        foreach ($orderBy as $column => $value) {
            if ($first) {
                $queryBuilder->orderBy("t.$column", $value);
                $first = false;
            } else {
                $queryBuilder->addOrderBy("t.$column", $value);
            }
        }
        if ($limit > 0 && is_int($limit)) {
            $queryBuilder->setMaxResults($limit);
        }
        if ($offset > 0 && is_int($offset)) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder;
    }

    /**
     * Finds Tasks entities with joins to increase performance
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return array The objects.
     */
    public function findByWithJoins(array $criteria, array $orderBy = [], $limit = null, $offset = null)
    {
        $queryBuilder = $this->findByWithJoinsQuery($criteria, $orderBy, $limit, $offset);
        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all Tasks entities in the repository with joins to increase performance
     *
     * @return array The entities.
     */
    public function findAllWithJoinsQuery(int $limit = 100, int $offset = 0)
    {
        return $this->findByWithJoinsQuery([], ["completed" => "ASC"], $limit, $offset);
    }

    /**
     * Finds all Tasks entities in the repository with joins to increase performance
     *
     * @return array The entities.
     */
    public function findAllWithJoins(int $limit = 100, int $offset = 0)
    {
        return $this->findByWithJoins([], ["completed" => "ASC"], $limit, $offset);
    }

    public function findByAccountNoWorklog(Accounts $account)
    {
        return $this->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('wl.id IS NULL')
            ->andWhere('a.id = :account')
            ->setParameter(':account', $account)
            ->getQuery()
            ->getResult();
    }

    public function focusListLimit()
    {
        $today = new DateTime();
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

    /**
     *
     * @return array The objects.
     */
    public function findTasksYears()
    {
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->select('YEAR(t.completedAt) as years')
            ->groupBy('years');

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     *
     * @return array The objects.
     */
    public function findWorkingHoursPerMonth()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t.completedAt, SUM(t.duration)/60 as duration, CONCAT(YEAR(t.completedAt), MONTH(t.completedAt)) as DayCompletedAt')
            ->where('t.completedAt is not null')
            ->andWhere('t.duration is not null')
            ->orderBy('t.completedAt')
            ->groupBy('DayCompletedAt')
            ->getQuery()
            ->getResult();
    }

    /**
     * List of completed tasks on a date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getCompletedByDate(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('DATE(t.completedAt) = :date')
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * List of created tasks on a date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getCreatedByDate(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('DATE(t.createdAt) = :date')
            ->andWhere('t.completed <> TRUE')
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * List of updated tasks on a date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getUpdatedByDate(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('DATE(t.updatedAt) = :date')
            ->andWhere('t.completed <> TRUE')
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getCompletedByMonthOrDay($year, $month, $day = false)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('YEAR(t.completedAt) = :year')
            ->andWhere('MONTH(t.completedAt) = :month')
            ->setParameter(":year", $year)
            ->setParameter(":month", $month);

        if ($day) {
            $qb->andWhere('DAY(t.completedAt) = :day')
                ->setParameter('day', $day);
        }
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    public function getDurationSumByRange($from, $to)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.duration)')
            ->where('t.completedAt BETWEEN :from AND :to')
            ->setParameter(":from", $from)
            ->setParameter(":to", $to);

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     *
     * @param int $limit
     * @return Tasks[]
     */
    public function findFocusByEta($date, $limit = 0)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('t.workLog', 'wl')
            ->where('t.completed <> true')
            ->andWhere('DATE(t.eta) <= :date OR t.eta IS NULL')
            ->orderBy("t.urgency", "DESC")
            ->addOrderBy("t.priority", "DESC")
            ->addOrderBy("t.order", "ASC")
            ->setParameter(':date', $date->format('Y-m-d'));

        if ($limit > 0 && is_int($limit)) {
            $query->setMaxResults($limit);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    /**
     * List of created tasks on a date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getOpenCreatedBeforeDate(DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, r, wl')
            ->leftJoin('t.workLog', 'wl')
            ->leftJoin('t.taskList', 'tl')
            ->leftJoin('tl.account', 'a')
            ->leftJoin('a.client', 'c')
            ->leftJoin('c.rates', 'r')
            ->where('DATE(t.createdAt) >= :date')
            ->andWhere('t.completed <> TRUE')
            ->orderBy('t.createdAt')
            ->setParameter(':date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

}
