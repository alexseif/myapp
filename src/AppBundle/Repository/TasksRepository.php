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
    const NOT_COMPLETED = 't.completed <> true';
    const URGENCY = 't.urgency';
    const PRIORTIY = 't.priority';
    const ORDER = 't.order';
    const SELECT = 't, tl, a, c, r, wl';
    const MYSQL_DATE_FORMAT = 'Y-m-d H:i';
    const TASKLIST = 't.taskList';
    const WORKLOG = 't.workLog';
    const ETA_TODAY = 't.eta <= :today OR t.eta IS NULL';
    const TODAY = ':today';
    const DATE = ':date';
    const COMPLETED_AFTER_DATE = 't.completedAt > :date';
    const ACCOUNT = 'tl.account';
    const CLIENT = 'a.client';
    const RATES = 'c.rates';
    const COMPLETEDAT = 't.completedAt';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }


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
            ->where(self::NOT_COMPLETED)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->getQuery()
            ->getResult();
    }


    public function getIncopmleteTasks()
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('t.completedAt >= :date')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::COMPLETEDAT, "ASC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::DATE, $date)
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
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('t.completedAt >= :date')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::COMPLETEDAT, "ASC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::DATE, $date)
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
            ->where(self::COMPLETED_AFTER_DATE)
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::DATE, $date->format(self::MYSQL_DATE_FORMAT))
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
//    $week_start = date(self::MYSQL_DATE_FORMAT, strtotime('-' . $day . ' days'));
//    $week_end = date(self::MYSQL_DATE_FORMAT, strtotime('+' . (6 - $day) . ' days'));

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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy("tl.id", "ASC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT));

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


    /**
     * @param \DateTime $date
     * @param int $limit
     * @param int $offset
     * @return Tasks[]
     */
    public function focusListWithMeAndDate($date)
    {
        $limit = 25;
        $offset = 0;
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->where(self::NOT_COMPLETED)
            ->andWhere('DATE(t.eta) <= :date OR t.eta IS NULL')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy("tl.id", "ASC")
            ->setParameter(self::DATE, $date->format('Y-m-d'));

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
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setMaxResults(30)
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getResult();
    }

    public function focusByTasklist($taskList)
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->andWhere('t.taskList = :tasklist')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->andWhere('a.client = :client')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
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
            ->where(self::NOT_COMPLETED)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->groupBy(self::URGENCY)
            ->addGroupBy(self::PRIORTIY)
            ->getQuery()
            ->getResult();
    }

    public function sumByUrgenctAndPriority()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration), t.urgency, t.priority')
            ->where(self::NOT_COMPLETED)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->groupBy(self::URGENCY)
            ->addGroupBy(self::PRIORTIY)
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
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->addOrderBy("rand", "ASC")
            ->setMaxResults(5)
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getResult();
    }

    public function singleTaskList()
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy("t.taskList", "DESC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getResult();
    }

    public function weightedList()
    {
        $today = new DateTime();
        return $this
            ->createQueryBuilder('t')
            ->select('COUNT(t.urgency) as cnt, t.urgency, t.priority, tl.id')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->leftJoin(self::TASKLIST, 'tl')
            ->groupBy('t.urgency, t.priority, tl.id')
            ->orderBy('t.urgency', 'DESC')
            ->addOrderBy(self::PRIORTIY, 'DESC')
            ->addOrderBy('cnt', 'DESC')
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt IS NOT NULL')
            ->setParameter(':client', $client)
            ->orderBy(self::COMPLETEDAT)
            ->getQuery()
            ->getResult();
    }

    public function findDurationCompletedByClientByRange($client, $from, $to)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration) as duration')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere(self::COMPLETED_AFTER_DATE)
            ->setParameter(':client', $client)
            ->setParameter(self::DATE, $date->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere('date(t.completedAt) = :date')
            ->orderBy(self::COMPLETEDAT)
            ->setParameter(':client', $client)
            ->setParameter(self::DATE, $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findCompletedByClientByRange($client, $from, $to)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere('t.completedAt BETWEEN :from AND :to')
            ->orderBy(self::COMPLETEDAT)
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere('YEAR(t.completedAt) = :year')
            ->andWhere('MONTH(t.completedAt) = :month')
            ->orderBy(self::COMPLETEDAT)
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere(self::COMPLETED_AFTER_DATE)
            ->setParameter(':client', $client)
            ->setParameter(self::DATE, $date->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumDurationByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('SUM(t.duration) as duration')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where('a.client = :client')
            ->andWhere('date(t.completedAt) = :date')
            ->setParameter(':client', $client)
            ->setParameter(self::DATE, $date->format('Y-m-d'))
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'w');

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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('wl.id IS NULL')
            ->andWhere('a.id = :account')
            ->setParameter(':account', $account)
            ->getQuery()
            ->getResult();
    }

    public function focusListLimit()
    {
        return $this->getIncopmleteTasks();
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
            ->orderBy(self::COMPLETEDAT)
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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('DATE(t.completedAt) = :date')
            ->setParameter(self::DATE, $date->format('Y-m-d'))
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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('DATE(t.createdAt) = :date')
            ->andWhere(self::NOT_COMPLETED)
            ->setParameter(self::DATE, $date->format('Y-m-d'))
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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('DATE(t.updatedAt) = :date')
            ->andWhere(self::NOT_COMPLETED)
            ->setParameter(self::DATE, $date->format('Y-m-d'))
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

    /**
     * @param $from
     * @param $to
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDurationSumByRange($from, $to)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.duration)')
            ->where('t.completedAt BETWEEN :from AND :to')
            ->setParameter(":from", $from)
            ->setParameter(":to", $to);

        return (int)$qb->getQuery()
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
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->where(self::NOT_COMPLETED)
            ->andWhere('DATE(t.eta) <= :date OR t.eta IS NULL')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::DATE, $date->format('Y-m-d'));

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
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->where('DATE(t.createdAt) >= :date')
            ->andWhere(self::NOT_COMPLETED)
            ->orderBy('t.createdAt')
            ->setParameter(self::DATE, $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * Task average duration
     *
     * @return float Task average duration.
     */
    public function getTaskAverageDuration()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('avg(t.duration)')
            ->where('t.completed = TRUE')
            ->getQuery()
            ->getSingleScalarResult();
    }


}
