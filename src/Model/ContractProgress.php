<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Model;

use App\Entity\Contract;
use App\Repository\TasksRepository;
use App\Util\DateRanges;
use DateTime;

/**
 * Description of ContractProgress.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ContractProgress
{

    /**
     * @var float
     */
    private $contractProgress;

    private $todaysProgress;

    private $monthProgress;

    /**
     * @var int
     */
    private $dailyTarget;

    private $monthlyMinutes;

    private $workedMinutes;

    private $workingDaysSoFar;

    private $remainingDays;

    private $minutesPerDay;

    private $workedMinutesToday;

    /**
     * @var \DateTime
     */
    private $monthStart;

    private $monthEnd;

    public const DAYS_PER_MONTH = 22;

    public function __construct(
      Contract $contract,
      TasksRepository $tasksRepository
    ) {
        $this->minutesPerDay = $contract->getHoursPerDay() * 60;
        $this->monthlyMinutes = self::DAYS_PER_MONTH * $this->minutesPerDay;

        $this->monthStart = DateRanges::getMonthStart(
          'now',
          $contract->getBilledOn()
        );
        if ($contract->getBilledOn()) {
            $this->monthStart->setDate(
              $this->monthStart->format('Y'),
              $this->monthStart->format('m'),
              $contract->getBilledOn()
            );
        }
        $this->monthEnd = clone $this->monthStart;
        $this->monthEnd->modify('+1 month');
        $this->workedMinutes = $tasksRepository->findDurationCompletedByClientByRange(
          $contract->getClient(),
          $this->getMonthStart(),
          $this->getMonthEnd()
        );
        $this->workedMinutesToday = $tasksRepository->findCompletedByClientToday(
          $contract->getClient()
        );
        $this->calcContractProgress();

        //@TODO: holidays and stuff
        //@TODO: WorkingDaySoFar by billing date
        $this->workingDaysSoFar = DateRanges::numberOfWorkingDays(
          $this->getMonthStart(),
          new DateTime()
        );
        $this->remainingDays = (self::DAYS_PER_MONTH - $this->getWorkingDaysSoFar(
          )) ?: 1;
        $this->monthProgress = $this->getWorkingDaysSoFar(
          ) / self::DAYS_PER_MONTH * 100;
        $this->dailyTarget = ($this->getMonthlyMinutes(
            ) - $this->getWorkedMinutes()) / $this->getRemainingDays();

        $this->todaysProgress = $this->workedMinutesToday / $this->minutesPerDay * 100;
    }

    public function getContractProgress(): float
    {
        return $this->contractProgress;
    }

    public function calcContractProgress(): void
    {
        $this->contractProgress = $this->getWorkedMinutes(
          ) / $this->getMonthlyMinutes() * 100;
    }

    public function getTodaysProgress(): float
    {
        return $this->todaysProgress;
    }

    public function getDailyTarget(): int
    {
        return $this->dailyTarget;
    }

    public function getMonthStart(): DateTime
    {
        return $this->monthStart;
    }

    public function getMonthEnd(): DateTime
    {
        return $this->monthEnd;
    }

    public function getMonthlyMinutes(): int
    {
        return $this->monthlyMinutes;
    }

    public function getWorkedMinutes(): int
    {
        return $this->workedMinutes;
    }

    public function getWorkingDaysSoFar(): int
    {
        return $this->workingDaysSoFar;
    }

    public function getRemainingDays(): int
    {
        return $this->remainingDays;
    }

    /**
     * @return float|int
     */
    public function getMonthProgress()
    {
        return $this->monthProgress;
    }

    /**
     * @param float|int $monthProgress
     */
    public function setMonthProgress($monthProgress): void
    {
        $this->monthProgress = $monthProgress;
    }


}
