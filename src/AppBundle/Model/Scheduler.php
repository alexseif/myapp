<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace AppBundle\Model;

use AppBundle\Entity\Contract;
use AppBundle\Entity\Tasks;
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
        foreach ($this->period as $dt) {
            $this->loadContractDayLength();
            $this->addDay($this->generateDay($dt));
        }
    }

    public function generateDay($date): Day
    {
        $day = new Day();
        $day->setDate($date);
        $day->setIsToday(($this->today->format('Y-m-d') == $day->getDate()->format('Y-m-d')));
        //Completed Tasks
        $completedTasks = $this->getCompletedTasks($date);
        foreach ($completedTasks as $task) {
            $day->addTask($task);
            if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
                if ($task->getCompleted()) {
                    $mins = $task->getDuration();
                }
                $this->contractDayLength[$task->getClient()->getId()] -= $mins;
            }
        }

        $this->loadScheduledTasks($day);

        if ($day->getDate() >= $this->today) {
            $this->loadContractsTasks($day);
            $this->loadTasks($day);
        }

        return $day;
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

    public function loadContractTasks(Day &$day, $contract)
    {
        $contractTasks = $this->tasksRepository->focusListByClientAndDate($contract->getClient(),
            $day->getDate(),
            $this->tasked);
        foreach ($contractTasks as $task) {
            if ($this->contractWeekLength[$task->getClient()->getId()] > 0) {
                if ($day->addTask($task)) {
                    $this->updateTasks($task);
                } else {
                    break;
                }
            }
        }
    }

    public function loadContractsTasks(Day &$day)
    {
        //Contract Tasks
        foreach ($this->contracts as $contract) {
            if ($this->contractWeekLength[$contract->getClient()->getId()] > 0) {
                $this->loadContractTasks($day, $contract);
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
