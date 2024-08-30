<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace App\Model;

use App\Entity\Contract;
use App\Entity\Tasks;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class Scheduler
{
    public $dayLength = [];
    public $contractDayLength = [];
    public $contractWeekLength = [];
    public $contracts;
    /**
     * @var ArrayCollection
     */
    public $days;
    public $tasked = [];
    public $date;
    public $today;
    public $tasksRepository;
    public $contractRepository;
    public $year;
    public $week;
    public $period;

    public function __construct(EntityManagerInterface $entityManager, $year, $week)
    {
        $this->tasksRepository = $entityManager->getRepository(Tasks::class);
        $this->contractRepository = $entityManager->getRepository(Contract::class);
        $this->year = $year;
        $this->week = $week;
        $this->days = new ArrayCollection();
        $this->period = $this->getPeriod();
        $this->setToday();
        $this->loadContracts();
        $this->loadContractWeekLength();
    }

    public function generate($concatenated = false)
    {
        foreach ($this->period as $dt) {
            $day = new Day();
            $this->dayPreset($day, $dt);
            if ($day->getDate() >= $this->today) {
                $this->loadContractsTasks($day, $concatenated);
                $this->loadTasks($day);
            }
            $this->addDay($day);
        }
    }

    public function dayPreset(Day &$day, $date)
    {
        $this->loadContractDayLength();
        $day->setDate($date);
        $day->setIsToday(($this->today->format('Y-m-d') == $day->getDate()->format('Y-m-d')));
        $this->loadCompletedTasks($day);
        $this->loadScheduledTasks($day);

    }

    public function getPeriod()
    {
        $dto = new \DateTime();
        $dto->setISODate($this->year, $this->week);
        $dto->modify('-1 days');
        $week_start = clone $dto;
        $dto->modify('+5 days');
        $week_end = clone $dto;
        $interval = \DateInterval::createFromDateString('1 day');

        return new \DatePeriod($week_start, $interval, $week_end);
    }

    public function setToday()
    {
        $this->today = new \DateTime();
        $this->today->setTime(0, 0, 0);
    }

    public function getCompletedTasks($date)
    {
        return $this->tasksRepository->getCompletedByDate($date);
    }

    public function loadCompletedTasks(Day &$day)
    {
        //Completed Tasks
        $completedTasks = $this->getCompletedTasks($day->getDate());
        foreach ($completedTasks as $task) {
            $day->addTask($task);
            if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
                if ($task->getCompleted()) {
                    $mins = $task->getDuration();
                }
                $this->contractDayLength[$task->getClient()->getId()] -= $mins;
            }
        }
    }

    public function loadScheduledTasks(Day &$day)
    {
        //Scheduled Tasks
        $scheduledTasks = $this->tasksRepository->findBySchedule($day->getDate());
        foreach ($scheduledTasks as $task) {
            if ($day->addTask($task)) {
                $this->updateTasks($task);
            } else {
                break;
            }
        }
    }


    public function loadContractsTasks(Day &$day, $concatenated)
    {
        //Contract Tasks
        foreach ($this->contracts as $contract) {
            $contractTasks = $this->tasksRepository->focusListByClientAndDate($contract->getClient(),
                $day->getDate(),
                $this->tasked);
            foreach ($contractTasks as $task) {
                if ((($this->contractWeekLength[$task->getClient()->getId()] > 0) && $concatenated) || (($this->contractDayLength[$task->getClient()->getId()] > 0) && !$concatenated)) {
                    if ($day->addTask($task)) {
                        $this->updateTasks($task);
                    } else {
                        break;
                    }
                }
            }
        }
    }


    public function loadTasks(&$day)
    {
        //Tasks (unscheduled)
        $focusTasks = $this->tasksRepository->focusListScheduler($day->getDate(), $this->tasked);
        foreach ($focusTasks as $task) {
            if ((array_key_exists($task->getClient()->getId(),
                        $this->contractDayLength) && $this->contractDayLength[$task->getClient()->getId()] > 0) || $day->getDayLength() > 0) {
                if ($day->addTask($task)) {
                    $this->updateTasks($task);
                } else {
                    break;
                }
            }
        }
    }

    function updateTasks(Tasks $task)
    {
        $this->tasked[] = $task->getId();
        if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
            if ($task->getCompleted()) {
                $mins = $task->getDuration();
            } else {
                $mins = ($task->getEst()) ?: 60;
            }
            $this->contractDayLength[$task->getClient()->getId()] -= $mins;
            $this->contractWeekLength[$task->getClient()->getId()] -= $mins;
        }
    }

    public function loadContracts()
    {
        $this->contracts = $this->contractRepository->findBy(['isCompleted' => false]);
    }

    public function loadContractDayLength()
    {
        foreach ($this->contracts as $contract) {
            $this->contractDayLength[$contract->getClient()->getId()] = $contract->getHoursPerDay() * 60;
        }
    }

    public function loadContractWeekLength()
    {
        foreach ($this->contracts as $contract) {
            $this->contractWeekLength[$contract->getClient()->getId()] = ($contract->getHoursPerDay() * 60 * 5);
        }
    }


    public function getDays(): ArrayCollection
    {
        return $this->days;
    }

    public function setDays(ArrayCollection $days): void
    {
        $this->days = $days;
    }

    /**
     * @return void
     */
    public function addDay(Day $day)
    {
        $this->days->add($day);
    }
}
