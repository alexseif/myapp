<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\AccountTransactions;
use AppBundle\Entity\Tasks;
use AppBundle\Util\DateRanges;
use AppBundle\Util\Formatting;
use DateTime;
use Doctrine\ORM\EntityManager;

/**
 * Description of Bottom Bar Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ProgressMonitoring
{

    /**
     * @todo: remove and use repository in entity service
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @todo rename
     * @var CostService $cs
     */
    protected $cs;

    /**
     * @todo move to seperate service
     * @var int total number of clients
     */
    private $clientsCount;

    /**
     * @todo move to seperate service
     * @var float clients annual increase
     */
    private $clientsProgress;

    /**
     * @todo move to seperate service
     * @var int total number of accounts
     */
    private $accountsCount;

    /**
     * @todo move to seperate service
     * @var float accounts annual increase
     */
    private $accountsProgress;

    /**
     * @todo move to seperate service
     * @var int total number of tasks this month
     */
    private $tasksCompletedCount;

    /**
     * @todo move to seperate service
     * @var float tasks this month increase
     */
    private $tasksCompletedProgress;

    /**
     * @todo move to seperate service
     * @var float revenue sum this month
     */
    private $revenueSum;

    /**
     * @todo move to seperate service
     * @var float revenue this month increase
     */
    private $revenueProgress;

    /**
     * @todo move to seperate service
     * @var int duration sum this month
     */
    private $durationSum;

    /**
     * @todo move to seperate service
     * @var int duration this month increase
     */
    private $durationProgress;

    /**
     * @todo move to seperate service
     * @var array
     */
    private $earnedProgress;

    /**
     * @todo move to seperate service
     * @var array
     */
    private $averageReport;


    /**
     * ProgressMonitoring constructor.
     * @param EntityManager $em
     * @param CostService $cs
     */
    public function __construct(EntityManager $em, CostService $cs)
    {
        $this->em = $em;
        $this->cs = $cs;
        $this->setClientsCount();
        $this->setClientsProgress();
        $this->setAccountsCount();
        $this->setAccountsProgress();
        $this->setTasksCompletedCount();
        $this->setTasksCompletedProgress();
        $this->setRevenueSum();
        $this->setRevenueProgress();
        $this->setDurationSum();
        $this->setDurationProgress();
        $this->setEarnedThisMonth();
        $this->setEarnedThisWeek();
        $this->setEarnedToday();
        $this->setAverageReport();
    }

    /**
     * @return CostService
     */
    public function getCostOfLife()
    {
        return $this->cs;
    }

    /**
     * @param $number
     * @return string
     */
    public function formatNumber($number)
    {
        return Formatting::number($number);
    }

    /**
     * @todo
     */
    public function setClientsCount()
    {
        $this->clientsCount = count($this->em->getRepository('AppBundle:Client')->findBy([
            "enabled" => true]));
    }

    /**
     * @return int
     */
    public function getClientsCount()
    {
        return $this->clientsCount;
    }

    /**
     * @todo
     */
    public function setClientsProgress()
    {
        $date = new DateTime();
        $date->modify("-1 year");
        $clientsLastYear = $this->em->getRepository('AppBundle:Client')
            ->getCreatedTillYear($date->format('Y'));
        $this->clientsProgress = (($this->clientsCount - $clientsLastYear) / $clientsLastYear) * 100;
    }

    /**
     * @return string
     */
    function getClientsProgress()
    {
        return $this->formatNumber($this->clientsProgress);
    }

    /**
     * @todo
     */
    public function setAccountsCount()
    {
        $this->accountsCount = count($this->em->getRepository('AppBundle:Accounts')->findAll());
    }

    /**
     * @return int
     */
    public function getAccountsCount()
    {
        return $this->accountsCount;
    }

    /**
     * @todo
     */
    public function setAccountsProgress()
    {
        $date = new DateTime();
        $date->modify("-1 year");
        $accountsLastYear = $this->em->getRepository('AppBundle:Accounts')
            ->getCreatedTillYear($date->format('Y'));
        $this->accountsProgress = (($this->accountsCount - $accountsLastYear) / $accountsLastYear) * 100;
    }

    /**
     * @return string
     */
    function getAccountsProgress()
    {
        return $this->formatNumber($this->accountsProgress);
    }

    /**
     * @todo
     */
    public function setTasksCompletedCount()
    {
        $date = DateRanges::getMonthStart();
        $this->tasksCompletedCount = $this->em->getRepository(Tasks::class)
            ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'));
    }

    /**
     * @return int
     */
    function getTasksCompletedCount()
    {
        return $this->tasksCompletedCount;
    }

    /**
     * @todo
     */
    public function setTasksCompletedProgress()
    {
        $date = DateRanges::getMonthStart();
        $tasksLastMonth = $this->em->getRepository(Tasks::class)
            ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'), $date->format('d'));
        $divisionByZero = $tasksLastMonth ? $tasksLastMonth : 1;

        $this->tasksCompletedProgress = (($this->tasksCompletedCount - $tasksLastMonth) / $divisionByZero) * 100;
    }

    /**
     * @return string
     */
    function getTasksCompletedProgress()
    {
        if ($this->tasksCompletedProgress >= 1000) {
            return $this->formatNumber($this->tasksCompletedProgress / 1000) . 'k';
        }
        return $this->formatNumber($this->tasksCompletedProgress);
    }

    /**
     * @todo
     */
    public function setRevenueSum()
    {
        $from = DateRanges::getMonthStart();
        $from->modify("-1 month");
        $to = DateRanges::getMonthStart();
        $this->revenueSum = $this->em->getRepository('AppBundle:AccountTransactions')
            ->getRevenueSumByDateRange($from, $to);
    }

    /**
     * @return string
     */
    function getRevenueSum()
    {
        if ($this->revenueSum <= 1000) {
            return $this->formatNumber($this->revenueSum / 1000) . 'k';
        }
        return $this->formatNumber($this->revenueSum);
    }

    /**
     * @todo
     */
    function setRevenueProgress()
    {
        $from =
            DateRanges::getMonthStart();
        $from->modify("-2 months");
        $to = DateRanges::getMonthStart();
        $to->modify("-1 month");
        $revenueLastMonth = $this->em->getRepository('AppBundle:AccountTransactions')
            ->getRevenueSumByDateRange($from, $to);

        $divisionByZero = $revenueLastMonth ? $revenueLastMonth : 1;
        $this->revenueProgress = (($this->revenueSum - $revenueLastMonth) / $divisionByZero) * 100;
    }

    /**
     * @return string
     */
    function getRevenueProgress()
    {
        return $this->formatNumber($this->revenueProgress);
    }

    /**
     * @todo
     */
    function setDurationSum()
    {
        $from = DateRanges::getMonthStart();
        $from->modify("-1 month");
        $to = DateRanges::getMonthStart();

        $this->durationSum = $this->em->getRepository(Tasks::class)
            ->getDurationSumByRange($from, $to);
    }

    /**
     * @return string
     */
    function getDurationSum()
    {
        return $this->formatNumber($this->durationSum / 60) . ':' . ($this->durationSum % 60);
    }

    /**
     * @todo
     */
    function setDurationProgress()
    {
        $from = DateRanges::getMonthStart();
        $from->modify("-2 months");
        $to = DateRanges::getMonthStart();
        $to->modify("-1 month");
        $durationLastMonth = $this->em->getRepository(Tasks::class)
            ->getDurationSumByRange($from, $to);
        $divisionByZero = $durationLastMonth ? $durationLastMonth : 1;
        $this->durationProgress = ((($this->durationSum - $durationLastMonth) / $divisionByZero) * 100);
    }

    /**
     * @return string
     */
    function getDurationProgress()
    {
        return $this->formatNumber($this->durationProgress);
    }

    /**
     * @todo
     */
    public function setEarnedToday()
    {
        $completedTasks = $this->em->getRepository(Tasks::class)->getCompletedToday();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null != $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['today'] = $total;
    }

    /**
     * @return string
     */
    public function getEarnedToday()
    {
        return $this->formatNumber($this->earnedProgress['today']);
    }

    /**
     * @todo
     */
    public function setEarnedThisWeek()
    {
        $completedTasks = $this->em->getRepository(Tasks::class)->getCompletedThisWeek();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['week'] = $total;
    }

    /**
     * @return string
     */
    public function getEarnedThisWeek()
    {
        return $this->formatNumber($this->earnedProgress['week']);
    }

    /**
     * @todo
     */
    public function setEarnedThisMonth()
    {
        $completedTasks = $this->em->getRepository(Tasks::class)->getCompletedThisMonth();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['month'] = $total;
    }

    /**
     * @return string
     */
    public function getEarnedThisMonth()
    {
        return $this->formatNumber($this->earnedProgress['month']);
    }

    /**
     * @return string
     */
    public function getMonthly()
    {
        return $this->formatNumber($this->getCostOfLife()->getMonthly());
    }

    /**
     * @return string
     */
    public function getWeekly()
    {
        return $this->formatNumber($this->getCostOfLife()->getWeekly());
    }

    /**
     * @return string
     */
    public function getDaily()
    {
        return $this->formatNumber($this->getCostOfLife()->getDaily());
    }

    /**
     * @return string
     */
    public function getHourly()
    {
        return $this->formatNumber($this->getCostOfLife()->getHourly());
    }

    /**
     * @return string
     */
    public function getMonthProgress()
    {
        return $this->formatNumber($this->earnedProgress['month'] / $this->getCostOfLife()->getMonthly() * 100);
    }

    /**
     * @return string
     */
    public function getWeekProgress()
    {
        return $this->formatNumber($this->earnedProgress['week'] / $this->getCostOfLife()->getWeekly() * 100);
    }

    /**
     * @return string
     */
    public function getTodayProgress()
    {
        return $this->formatNumber($this->earnedProgress['today'] / $this->getCostOfLife()->getDaily() * 100);
    }

    /**
     * @return array
     */
    public function getAverageReport(): array
    {
        return $this->averageReport;
    }

    /**
     * @param array $averageReport
     */
    public function setAverageReport(): void
    {

        $this->averageReport = [
            "avgDur" => $this->em->getRepository(Tasks::class)->getTaskAverageDuration(),
            "avgIncome" => $this->em->getRepository(AccountTransactions::class)->getAverage(),
        ];

    }

}
