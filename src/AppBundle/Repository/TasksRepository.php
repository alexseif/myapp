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
 */
class TasksRepository extends ServiceEntityRepository
{
    const NOT_COMPLETED = 't.completed <> true';
    const URGENCY = 't.urgency';
    const PRIORTIY = 't.priority';
    const ORDER = 't.order';
    const SELECT = 't, tl, a, c, r, wl, s';
    const MYSQL_DATE_FORMAT = 'Y-m-d H:i';
    const TASKLIST = 't.taskList';
    const WORKLOG = 't.workLog';
    const SCHEDULE = 't.schedule';
    const ETA_TODAY = 't.eta <= :today OR t.eta IS NULL';
    const ETA_DATE = 'DATE(t.eta) <= :date OR t.eta IS NULL';
    const COMPLETED_RANGE = 't.completedAt BETWEEN :from AND :to';
    const TODAY = ':today';
    const DATE = ':date';
    const FROM = ':from';
    const COMPLETED_AFTER_DATE = 't.completedAt > :date';
    const ACCOUNT = 'tl.account';
    const CLIENT = 'a.client';
    const CLIENT_VAR = ':client';
    const CLIENT_CLAUSE = self::CLIENT . ' = ' . self::CLIENT_VAR;
    const RATES = 'c.rates';
    const COMPLETEDAT = 't.completedAt';
    const TASKLISTID = "tl.id";
    const DURATION_SUM = 'SUM(t.duration) as duration';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    public function getQueryBuilder()
    {
        return $this
            ->createQueryBuilder('t')
            ->select(self::SELECT)
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::RATES, 'r')
            ->leftJoin(self::SCHEDULE, 's');
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
        return $this->getQueryBuilder()
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
     *
     * @param int $limit
     * @return Tasks[]
     */
    public function focusList($limit = 20, $offset = 0)
    {
        $today = new DateTime();
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->select(self::SELECT)
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::RATES, 'r')
            ->leftJoin(self::SCHEDULE, 's')
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_TODAY)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy(self::TASKLISTID, "ASC")
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
    public function focusListScheduler($date, $taskIds = [])
    {
        $limit = 25;
        $offset = 0;
        $queryBuilder = $this->getQueryBuilder()
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_DATE)
            ->andWhere('s.id IS NULL');
        if (count($taskIds)) {
            $queryBuilder->andWhere('t.id NOT IN (:taskIds)')
                ->setParameter(':taskIds', $taskIds);
        }
        $queryBuilder->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy(self::TASKLISTID, "ASC")
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

    /**
     * @param \DateTime $date
     * @param int $limit
     * @param int $offset
     * @return Tasks[]
     */
    public function focusListByClientAndDate($client, $date, $taskIds = [])
    {
        $queryBuilder = $this->getQueryBuilder()
            ->where(self::NOT_COMPLETED)
            ->andWhere(self::ETA_DATE)
            ->andWhere(self::CLIENT_CLAUSE)
            ->andWhere('s.id IS NULL')
            ->setParameter(self::CLIENT_VAR, $client);

        if (count($taskIds)) {
            $queryBuilder->andWhere('t.id NOT IN (:taskIds)')
                ->setParameter(':taskIds', $taskIds);
        }
        return $queryBuilder->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy(self::TASKLISTID, "ASC")
            ->setParameter(self::DATE, $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \DateTime $date
     * @param int $limit
     * @param int $offset
     * @return Tasks[]
     *
     */
    public function findBySchedule(\DateTime $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t, tl, a, c, wl, s')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->leftJoin(self::CLIENT, 'c')
            ->leftJoin(self::WORKLOG, 'wl')
            ->leftJoin(self::SCHEDULE, 's')
            ->where(self::NOT_COMPLETED)
            ->andWhere('DATE(s.eta) = DATE(:date)')
            ->andWhere('s.id IS NOT NULL')
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->addOrderBy(self::TASKLISTID, "ASC")
            ->setParameter(self::DATE, $date)
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
            ->andWhere(self::CLIENT_CLAUSE)
            ->orderBy(self::URGENCY, "DESC")
            ->addOrderBy(self::PRIORTIY, "DESC")
            ->addOrderBy(self::ORDER, "ASC")
            ->setParameter(self::TODAY, $today->format(self::MYSQL_DATE_FORMAT))
            ->setParameter(self::CLIENT_VAR, $client)
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
            ->where(self::CLIENT_CLAUSE)
            ->andWhere('t.completedAt IS NOT NULL')
            ->setParameter(self::CLIENT_VAR, $client)
            ->orderBy(self::COMPLETEDAT)
            ->getQuery()
            ->getResult();
    }

    public function findDurationCompletedByClientByRange($client, $from, $to)
    {
        return (int)$this
            ->createQueryBuilder('t')
            ->select(self::DURATION_SUM)
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where(self::CLIENT_CLAUSE)
            ->andWhere(self::COMPLETED_RANGE)
            ->setParameter(self::CLIENT_VAR, $client)
            ->setParameter(self::FROM, $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findCompletedByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where(self::CLIENT_CLAUSE)
            ->andWhere('date(t.completedAt) = :date')
            ->orderBy(self::COMPLETEDAT)
            ->setParameter(self::CLIENT_VAR, $client)
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
            ->where(self::CLIENT_CLAUSE)
            ->andWhere(self::COMPLETED_RANGE)
            ->orderBy(self::COMPLETEDAT)
            ->setParameter(self::CLIENT_VAR, $client)
            ->setParameter(self::FROM, $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getResult();
    }


    public function findCompletedByClientToday($client)
    {
        $date = new DateTime();
        $date->setTime(00, 00, 00);
        return $this
            ->createQueryBuilder('t')
            ->select(self::DURATION_SUM)
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where(self::CLIENT_CLAUSE)
            ->andWhere(self::COMPLETED_AFTER_DATE)
            ->setParameter(self::CLIENT_VAR, $client)
            ->setParameter(self::DATE, $date->format(self::MYSQL_DATE_FORMAT))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumDurationByClientByDate($client, $date)
    {
        return $this
            ->createQueryBuilder('t')
            ->select(self::DURATION_SUM)
            ->leftJoin(self::TASKLIST, 'tl')
            ->leftJoin(self::ACCOUNT, 'a')
            ->where(self::CLIENT_CLAUSE)
            ->andWhere('date(t.completedAt) = :date')
            ->setParameter(self::CLIENT_VAR, $client)
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
        return $this->getQueryBuilder()
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
        return $this->getQueryBuilder()
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
        return $this->getQueryBuilder()
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
        return $this->getQueryBuilder()
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
            ->select(self::DURATION_SUM)
            ->where(self::COMPLETED_RANGE)
            ->setParameter(":from", $from)
            ->setParameter(":to", $to);

        return (int)$qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * List of created tasks on a date
     *
     * @param DateTime $date
     * @return array The objects.
     */
    public function getOpenCreatedBeforeDate(DateTime $date)
    {
        return $this->getQueryBuilder()
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
