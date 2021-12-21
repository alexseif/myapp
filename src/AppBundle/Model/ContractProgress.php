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
        $this->workedMinutes = $tasksRepository->findDurationCompletedByClientByRange($contract->getClient(), $this->getMonthStart(), $this->getMonthEnd());
        $this->workedMinutesToday = $tasksRepository->findCompletedByClientToday($contract->getClient());
        $this->calcContractProgress();

        //@TODO: holidays and stuff
        //@TODO: WorkingDaySoFar by billing date
        $this->workingDaysSoFar = DateRanges::numberOfWorkingDays($this->getMonthStart(), new \DateTime());
        $this->remainingDays = (self::DAYS_PER_MONTH - $this->getWorkingDaysSoFar()) ?: 1;
        $this->dailyTarget = ($this->getMonthlyMinutes() - $this->getWorkedMinutes()) / $this->getRemainingDays();


        $this->todaysProgress = $this->workedMinutesToday / $this->minutesPerDay * 100;


    }

    /**
     * @return float
     */
    public function getContractProgress(): float
    {
        return $this->contractProgress;
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
     * @return int
     */
    public function getDailyTarget(): int
    {
        return $this->dailyTarget;
    }


    /**
     * @return \DateTime
     */
    public function getMonthStart(): \DateTime
    {
        return $this->monthStart;
    }


    /**
     * @return \DateTime
     */
    public function getMonthEnd(): \DateTime
    {
        return $this->monthEnd;
    }


    /**
     * @return int
     */
    public function getMonthlyMinutes(): int
    {
        return $this->monthlyMinutes;
    }


    /**
     * @return int
     */
    public function getWorkedMinutes(): int
    {
        return $this->workedMinutes;
    }


    /**
     * @return int
     */
    public function getWorkingDaysSoFar(): int
    {
        return $this->workingDaysSoFar;
    }


    /**
     * @return int
     */
    public function getRemainingDays(): int
    {
        return $this->remainingDays;
    }


}
