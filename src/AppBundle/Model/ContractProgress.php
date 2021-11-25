<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Model;

use AppBundle\Entity\Contract;
use AppBundle\Util\DateRanges;

/**
 * Description of ContractProgress
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ContractProgress
{


    /**
     * @var float
     */
    private $contractProgress, $todaysProgress;

    /**
     * @var int
     */
    private $dailyTarget, $monthlyMinutes, $workedMinutes, $workingDaysSoFar, $remainingDays, $minutesPerDay, $workedMinutesToday;


    /**
     * @var \DateTime
     */
    private $monthStart, $monthEnd;

    public const DAYS_PER_MONTH = 22;

    public function __construct(Contract $contract, $tasksRepository)
    {
        $this->minutesPerDay = $contract->getHoursPerDay() * 60;
        $this->monthlyMinutes = self::DAYS_PER_MONTH * $this->minutesPerDay;

        $this->monthStart = DateRanges::getMonthStart();
        if ($contract->getBilledOn()) {
            $this->monthStart->setDate($this->monthStart->format('Y'), $this->monthStart->format('m'), $contract->getBilledOn());
        }
        $this->monthEnd = clone $this->monthStart;
        $this->monthEnd->modify('+1 month');
        $this->setWorkedMinutes($tasksRepository->getDurationSumByRange($this->getMonthStart(), $this->getMonthEnd()));
        $this->workedMinutesToday = $tasksRepository->findCompletedByClientToday($contract->getClient());
        $this->calcContractProgress();

//@TODO: holidays and stuff
        $this->workingDaysSoFar = DateRanges::numberOfWorkingDays($this->getMonthStart(), new \DateTime());
        $this->remainingDays = (self::DAYS_PER_MONTH - $this->getWorkingDaysSoFar()) ?: 1;
        $this->dailyTarget = ($this->getMonthlyMinutes() - $this->getWorkedMinutes()) / $this->getRemainingDays();
        $this->setTodaysProgress($this->workedMinutesToday / $this->getDailyTarget() * 100);


    }

    /**
     * @return float
     */
    public function getContractProgress(): float
    {
        return $this->contractProgress;
    }

    /**
     * @param float $contractProgress
     */
    public function setContractProgress(float $contractProgress): void
    {
        $this->contractProgress = $contractProgress;
    }

    /**
     *
     */
    public function calcContractProgress(): void
    {
        $this->contractProgress = $this->getWorkedMinutes() / $this->getMonthlyMinutes() * 100;
    }

    /**
     * @return float
     */
    public function getTodaysProgress(): float
    {
        return $this->todaysProgress;
    }

    /**
     * @param float $todaysProgress
     */
    public function setTodaysProgress(float $todaysProgress): void
    {
        $this->todaysProgress = $todaysProgress;
    }

    /**
     * @return int
     */
    public function getDailyTarget(): int
    {
        return $this->dailyTarget;
    }

    /**
     * @param int $dailyTarget
     */
    public function setDailyTarget(int $dailyTarget): void
    {
        $this->dailyTarget = $dailyTarget;
    }

    /**
     * @return \DateTime
     */
    public function getMonthStart(): \DateTime
    {
        return $this->monthStart;
    }

    /**
     * @param \DateTime $monthStart
     */
    public function setMonthStart(\DateTime $monthStart): void
    {
        $this->monthStart = $monthStart;
    }

    /**
     * @return \DateTime
     */
    public function getMonthEnd(): \DateTime
    {
        return $this->monthEnd;
    }

    /**
     * @param \DateTime $monthEnd
     */
    public function setMonthEnd(\DateTime $monthEnd): void
    {
        $this->monthEnd = $monthEnd;
    }

    /**
     * @return int
     */
    public function getMonthlyMinutes(): int
    {
        return $this->monthlyMinutes;
    }

    /**
     * @param int $monthlyMinutes
     */
    public function setMonthlyMinutes(int $monthlyMinutes): void
    {
        $this->monthlyMinutes = $monthlyMinutes;
    }

    /**
     * @return int
     */
    public function getWorkedMinutes(): int
    {
        return $this->workedMinutes;
    }

    /**
     * @param int $workedMinutes
     */
    public function setWorkedMinutes(int $workedMinutes): void
    {
        $this->workedMinutes = $workedMinutes;
    }

    /**
     * @return int
     */
    public function getWorkingDaysSoFar(): int
    {
        return $this->workingDaysSoFar;
    }

    /**
     * @param int $workingDaysSoFar
     */
    public function setWorkingDaysSoFar(int $workingDaysSoFar): void
    {
        $this->workingDaysSoFar = $workingDaysSoFar;
    }

    /**
     * @return int
     */
    public function getRemainingDays(): int
    {
        return $this->remainingDays;
    }

    /**
     * @param int $remainingDays
     */
    public function setRemainingDays(int $remainingDays): void
    {
        $this->remainingDays = $remainingDays;
    }

    /**
     * @return int
     */
    public function getMinutesPerDay(): int
    {
        return $this->minutesPerDay;
    }

    /**
     * @param int $minutesPerDay
     */
    public function setMinutesPerDay(int $minutesPerDay): void
    {
        $this->minutesPerDay = $minutesPerDay;
    }

    /**
     * @return int
     */
    public function getWorkedMinutesToday(): int
    {
        return $this->workedMinutesToday;
    }

    /**
     * @param int $workedMinutesToday
     */
    public function setWorkedMinutesToday(int $workedMinutesToday): void
    {
        $this->workedMinutesToday = $workedMinutesToday;
    }


}
