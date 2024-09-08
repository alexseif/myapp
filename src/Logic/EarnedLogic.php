<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Logic;

use App\Entity\AccountTransactions;
use App\Entity\Tasks;

/**
 * Description of CostOfLifeLogic.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class EarnedLogic
{

    protected $em;

    protected $costOfLife;

    protected $issuedThisMonth;

    protected $monthly;

    protected $weekly;

    protected $daily = 0;

    public function __construct($em, $costOfLife)
    {
        $this->em = $em;
        $this->costOfLife = $costOfLife;
        $this->calculateDaily();
        $this->calculateWeekly();
        $this->calculateMonthly();
        $this->calculateIssued();
    }

    public function getEarned(): array
    {
        return [
          'daily' => $this->getDaily(),
          'weekly' => $this->getWeekly(),
          'monthly' => $this->getMonthly(),
        ];
    }

    public function calculateIssued(): void
    {
        $issued = 0;
        foreach (
          $this->em->getRepository(AccountTransactions::class)
            ->issuedThisMonth() as $tm
        ) {
            $issued += abs($tm->getAmount());
        }
        $this->setIssuedThisMonth($issued);
    }

    public function calculateMonthly(): void
    {
        $completedTasks = $this->em->getRepository(Tasks::class)
          ->getCompletedThisMonth();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (is_numeric($task->getRate())) ? $task->getRate(
            ) : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setMonthly($total);
    }

    public function calculateWeekly(): void
    {
        $completedTasks = $this->em->getRepository(Tasks::class)
          ->getCompletedThisWeek();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (is_numeric($task->getRate())) ? $task->getRate(
            ) : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setWeekly($total);
    }

    public function calculateDaily(): void
    {
        $completedTasks = $this->em->getRepository(Tasks::class)
          ->getCompletedToday();
        $total = 0;
        foreach ($completedTasks as $task) {
            $rate = (is_numeric($task->getRate())) ? $task->getRate(
            ) : $this->costOfLife->getHourly();
            $total += $task->getDuration() / 60 * $rate;
        }
        $this->setDaily($total);
    }

    public function getMonthly()
    {
        return $this->monthly;
    }

    public function getWeekly()
    {
        return $this->weekly;
    }

    public function getDaily()
    {
        return $this->daily;
    }

    public function setMonthly($monthly): void
    {
        $this->monthly = $monthly;
    }

    public function setWeekly($weekly): void
    {
        $this->weekly = $weekly;
    }

    public function setDaily($daily): void
    {
        $this->daily = $daily;
    }

    public function getIssuedThisMonth()
    {
        return $this->issuedThisMonth;
    }

    public function setIssuedThisMonth($issuedThisMonth): void
    {
        $this->issuedThisMonth = $issuedThisMonth;
    }

}
