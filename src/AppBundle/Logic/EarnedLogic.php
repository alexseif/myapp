<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

/**
 * Description of CostOfLifeLogic
 *
 * @author Alex Seif <me@alexseif.com>
 */
class EarnedLogic
{

    protected $em;
    protected $costOfLife;
    protected $issuedThisMonth, $monthly, $weekly, $daily = 0;

    public function __construct($em, $costOfLife)
    {
        $this->em = $em;
        $this->costOfLife = $costOfLife;
        $this->calculateDaily();
        $this->calculateWeekly();
        $this->calculateMonthly();
        $this->calculateIssued();
    }

    public function getEarned()
    {
        return [
            'daily' => $this->getDaily(),
            'weekly' => $this->getWeekly(),
            'monthly' => $this->getMonthly()
        ];
    }

    public function calculateIssued()
    {
        $issuedThisMonth = $this->em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
        $issued = 0;
        foreach ($issuedThisMonth as $tm) {
            $issued += abs($tm->getAmount());
        }
        $this->setIssuedThisMonth($issued);
    }

    public function calculateMonthly()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedThisMonth();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setMonthly($total);
    }

    public function calculateWeekly()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedThisWeek();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null == $task->getRate()) ? $task->getRate() : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setWeekly($total);
    }

    public function calculateDaily()
    {
        $completedTasks = $this->em->getRepository('AppBundle:Tasks')->getCompletedToday();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (null != $task->getRate()) ? $task->getRate() : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setDaily($total);
    }

    function getMonthly()
    {
        return $this->monthly;
    }

    function getWeekly()
    {
        return $this->weekly;
    }

    function getDaily()
    {
        return $this->daily;
    }

    function setMonthly($monthly)
    {
        $this->monthly = $monthly;
    }

    function setWeekly($weekly)
    {
        $this->weekly = $weekly;
    }

    function setDaily($daily)
    {
        $this->daily = $daily;
    }

    function getIssuedThisMonth()
    {
        return $this->issuedThisMonth;
    }

    function setIssuedThisMonth($issuedThisMonth)
    {
        $this->issuedThisMonth = $issuedThisMonth;
    }

}
