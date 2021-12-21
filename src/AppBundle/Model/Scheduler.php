<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Model;


use AppBundle\Entity\Contract;
use AppBundle\Entity\Schedule;
use AppBundle\Entity\Tasks;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Scheduler
{
    public $dayLength = [];
    public $contractDayLength = [];
    public $contracts;
    /**
     * @var ArrayCollection
     */
    public $days;
    public $tasked = [];
    public $date, $today;
    public $tasksRepository;
    public $contractRepository;
    public $year, $week;
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

        foreach ($this->period as $dt) {
            $this->loadContractDayLength();
            $this->addDay($this->generateDay($dt));
        }
    }
//TODO: Refactor
//TODO: Load sorted by contract per week
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
        //Scheduled Tasks
        $scheduledTasks = $this->tasksRepository->findBySchedule($date);
        foreach ($scheduledTasks as $task) {
            if ($day->addTask($task)) {
                $this->tasked[] = $task->getId();
                if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
                    if ($task->getCompleted()) {
                        $mins = $task->getDuration();
                    } else {
                        $mins = ($task->getEst()) ?: 60;
                    }
                    $this->contractDayLength[$task->getClient()->getId()] -= $mins;
                }
            } else {
                break;
            }
        }

        if ($day->getDate() >= $this->today) {
//Contract Tasks
            foreach ($this->contract as $contract) {
                if ($this->contractDayLength[$contract->getClient()->getId()] > 0) {
                    $contractTasks = $this->tasksRepository->focusListByClientAndDate($contract->getClient(), $date, $this->tasked);
                    foreach ($contractTasks as $task) {
                        if ($this->contractDayLength[$task->getClient()->getId()] > 0) {
                            if ($day->addTask($task)) {
                                $this->tasked[] = $task->getId();
                                if ($task->getCompleted()) {
                                    $mins = $task->getDuration();
                                } else {
                                    $mins = ($task->getEst()) ?: 60;
                                }
                                $this->contractDayLength[$task->getClient()->getId()] -= $mins;
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
            //Tasks (unscheduled)
            $focusTasks = $this->tasksRepository->focusListScheduler($date, $this->tasked);
            foreach ($focusTasks as $task) {
                if ((array_key_exists($task->getClient()->getId(), $this->contractDayLength) && $this->contractDayLength[$task->getClient()->getId()] > 0) || $day->getDayLength() > 0) {
                    if ($day->addTask($task)) {
                        $this->tasked[] = $task->getId();
                        if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
                            if ($task->getCompleted()) {
                                $mins = $task->getDuration();
                            } else {
                                $mins = ($task->getEst()) ?: 60;
                            }
                            $this->contractDayLength[$task->getClient()->getId()] -= $mins;
                        }
                    } else {
                        break;
                    }
                }
            }
        }
        return $day;
    }


    function getPeriod()
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

    public function loadScheduledTasks()
    {
        $scheduledTasks = $this->tasksRepository->findBySchedule($this->date);
        foreach ($scheduledTasks as $scheduledTask) {
            $this->updateTasks($scheduledTask);
        }
    }

    public function loadContracts()
    {
        $this->contract = $this->contractRepository->findBy(["isCompleted" => false]);
    }

    public function loadContractDayLength()
    {
        foreach ($this->contract as $contract) {
            $this->contractDayLength[$contract->getClient()->getId()] = $contract->getHoursPerDay() * 60;
        }
    }

    public function loadContractTasks()
    {
        foreach ($this->contract as $contract) {
            $contractTasks = $this->tasksRepository->focusListByClientAndDate($contract->getClient(), $this->date, $this->tasked);
            foreach ($contractTasks as $task) {
                if ($this->dayLength <= 0 || $this->contractDayLength[$contract->getClient()->getId()] <= 0) {
                    break;
                }
                $this->updateTasks($task);

            }
        }
    }

    public function updateTasks(Tasks $task)
    {
        if ($this->dayLength <= 0) {
            $this->tasks[] = $task;
            if ($task->getCompleted()) {
                $mins = $task->getDuration();
            } else {
                $this->tasked[] = $task->getId();
                $mins = ($task->getEst()) ?: 60;
            }
            $this->dayLength -= $mins;
            if (array_key_exists($task->getClient()->getId(), $this->contractDayLength)) {
                $this->contractDayLength[$task->getClient()->getId()] -= $mins;
            }
        }
    }

    public function loadTasks()
    {
        $focusTasks = $this->tasksRepository->focusListScheduler($this->date, $this->tasked);
        foreach ($focusTasks as $task) {
            if ($this->dayLength <= 0) {
                break;
            }
            $this->updateTasks($task);
        }

    }

    /**
     * @return ArrayCollection
     */
    public function getDays(): ArrayCollection
    {
        return $this->days;
    }

    /**
     * @param ArrayCollection $days
     */
    public function setDays(ArrayCollection $days): void
    {
        $this->days = $days;
    }

    /**
     * @param Day $day
     * @return void
     */
    public function addDay(Day $day)
    {
        $this->days->add($day);
    }

}
