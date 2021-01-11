<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\CostService;

/**
 * Description of Bottom Bar Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ProgressMonitoring
{

    /**
     *
     * @var EntityManager $em
     */
    protected $em;

    /**
     *
     * @var CostService $cs
     */
    protected $cs;

    /**
     *
     * @var int total number of clients
     */
    private $clientsCount;

    /**
     *
     * @var float clients annual increase
     */
    private $clientsProgress;

    /**
     *
     * @var int total number of accounts
     */
    private $accountsCount;

    /**
     *
     * @var float accounts annual increase
     */
    private $accountsProgress;

    /**
     *
     * @var int total number of tasks this month
     */
    private $tasksCompletedCount;

    /**
     *
     * @var float tasks this month increase
     */
    private $tasksCompletedProgress;

    /**
     *
     * @var float revenue sum this month
     */
    private $revenueSum;

    /**
     *
     * @var float revenue this month increase
     */
    private $revenueProgress;

    /**
     *
     * @var int duration sum this month
     */
    private $durationSum;

    /**
     *
     * @var int duration this month increase
     */
    private $durationProgress;

    /**
     *
     * @var array
     */
    private $earnedProgress;

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
    }

    public function getCostOfLife()
    {
        return $this->cs;
    }

    public function formatNumber($number)
    {
        return \AppBundle\Util\Formatting::number($number);
    }

    public function setClientsCount()
    {
        $this->clientsCount = count($this->em->getRepository('AppBundle:Client')->findBy([
            "enabled" => true]));
    }

    public function getClientsCount()
    {
        return $this->clientsCount;
    }

    public function setClientsProgress()
    {
        $date = new \DateTime();
        $date->modify("-1 year");
        $clientsLastYear = $this->em->getRepository('AppBundle:Client')
            ->getCreatedTillYear($date->format('Y'));
        $this->clientsProgress = (($this->clientsCount - $clientsLastYear) / $clientsLastYear) * 100;
    }

    function getClientsProgress()
    {
        return $this->formatNumber($this->clientsProgress);
    }

    public function setAccountsCount()
    {
        $this->accountsCount = count($this->em->getRepository('AppBundle:Accounts')->findAll());
    }

    public function getAccountsCount()
    {
        return $this->accountsCount;
    }

    public function setAccountsProgress()
    {
        $date = new \DateTime();
        $date->modify("-1 year");
        $accountsLastYear = $this->em->getRepository('AppBundle:Accounts')
            ->getCreatedTillYear($date->format('Y'));
        $this->accountsProgress = (($this->accountsCount - $accountsLastYear) / $accountsLastYear) * 100;
    }

    function getAccountsProgress()
    {
        return $this->formatNumber($this->clientsProgress);
    }

    public function setTasksCompletedCount()
    {
        $date = \AppBundle\Util\DateRanges::getMonthStart();
        $this->tasksCompletedCount = $this->em->getRepository('AppBundle:Tasks')
            ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'));
    }

    function getTasksCompletedCount()
    {
        return $this->tasksCompletedCount;
    }

    public function setTasksCompletedProgress()
    {
        $date = \AppBundle\Util\DateRanges::getMonthStart();
        $tasksLastMonth = $this->em->getRepository('AppBundle:Tasks')
            ->getCompletedByMonthOrDay($date->format('Y'), $date->format('m'), $date->format('d'));
        $divisionByZero = $tasksLastMonth ? $tasksLastMonth : 1;

        $this->tasksCompletedProgress = (($this->tasksCompletedCount - $tasksLastMonth) / $divisionByZero) * 100;
    }

    function getTasksCompletedProgress()
    {
        if ($this->tasksCompletedProgress >= 1000) {
            return $this->formatNumber($this->tasksCompletedProgress / 1000) . 'k';
        }
        return $this->formatNumber($this->tasksCompletedProgress);
    }

    public function setRevenueSum()
    {
        $from = \AppBundle\Util\DateRanges::getMonthStart();
        $from->modify("-1 month");
        $to = \AppBundle\Util\DateRanges::getMonthStart();
        $this->revenueSum = $this->em->getRepository('AppBundle:AccountTransactions')
            ->getRevenueSumByDateRange($from, $to);
    }

    function getRevenueSum()
    {
        return $this->formatNumber($this->revenueSum);
    }

    function setRevenueProgress()
    {
        $from = \AppBundle\Util\DateRanges::getMonthStart();
        $from->modify("-2 months");
        $to = \AppBundle\Util\DateRanges::getMonthStart();
        $to->modify("-1 month");
        $revenueLastMonth = $this->em->getRepository('AppBundle:AccountTransactions')
            ->getRevenueSumByDateRange($from, $to);

        $divisionByZero = $revenueLastMonth ? $revenueLastMonth : 1;
        $this->revenueProgress = (($this->revenueSum - $revenueLastMonth) / $divisionByZero) * 100;
    }

    function getRevenueProgress()
    {
        return $this->formatNumber($this->revenueProgress);
    }

    function setDurationSum()
    {
        $from = \AppBundle\Util\DateRanges::getMonthStart();
        $from->modify("-1 month");
        $to = \AppBundle\Util\DateRanges::getMonthStart();

        $this->durationSum = $this->em->getRepository('AppBundle:Tasks')
            ->getDurationSumByRange($from, $to);
    }

    function getDurationSum()
    {
        return $this->formatNumber($this->durationSum / 60) . ':' . ($this->durationSum % 60);
    }

    function setDurationProgress()
    {
        $from = \AppBundle\Util\DateRanges::getMonthStart();
        $from->modify("-2 months");
        $to = \AppBundle\Util\DateRanges::getMonthStart();
        $to->modify("-1 month");
        $durationLastMonth = $this->em->getRepository('AppBundle:Tasks')
            ->getDurationSumByRange($from, $to);
        $divisionByZero = $durationLastMonth ? $durationLastMonth : 1;
        $this->durationProgress = ((($this->durationSum - $durationLastMonth) / $divisionByZero) * 100);
    }

    function getDurationProgress()
    {
        return $this->formatNumber($this->durationProgress);
    }

    public function setEarnedToday()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedToday();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null != $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['today'] = $total;
    }

    public function getEarnedToday()
    {
        return $this->formatNumber($this->earnedProgress['today']);
    }

    public function setEarnedThisWeek()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedThisWeek();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['week'] = $total;
    }

    public function getEarnedThisWeek()
    {
        return $this->formatNumber($this->earnedProgress['week']);
    }

    public function setEarnedThisMonth()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedThisMonth();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->getCostOfLife()->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->earnedProgress['month'] = $total;
    }

    public function getEarnedThisMonth()
    {
        return $this->formatNumber($this->earnedProgress['month']);
    }

    public function getMonthly()
    {
        return $this->formatNumber($this->getCostOfLife()->getMonthly());
    }

    public function getWeekly()
    {
        return $this->formatNumber($this->getCostOfLife()->getWeekly());
    }

    public function getDaily()
    {
        return $this->formatNumber($this->getCostOfLife()->getDaily());
    }

    public function getHourly()
    {
        return $this->formatNumber($this->getCostOfLife()->getHourly());
    }

    public function getMonthProgress()
    {
        return $this->formatNumber($this->earnedProgress['month'] / $this->getCostOfLife()->getMonthly() * 100);
    }

    public function getWeekProgress()
    {
        return $this->formatNumber($this->earnedProgress['week'] / $this->getCostOfLife()->getWeekly() * 100);
    }

    public function getTodayProgress()
    {
        return $this->formatNumber($this->earnedProgress['today'] / $this->getCostOfLife()->getDaily() * 100);
    }

}
