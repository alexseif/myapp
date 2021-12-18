<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Model;


use AppBundle\Entity\Contract;
use AppBundle\Entity\Tasks;
use Doctrine\ORM\EntityManagerInterface;

class Scheduler
{
    public $dayLength = 540;
    public $iSoftLength = 120;
    public $contractDayLength = [];
    public $dayName;
    public $tasks;
    public $tasked = [];
    public $date, $today;
    public $isToday;
    public $tasksRepository;
    public $contractRepository;

    /**
     * @param $dayLength
     * @param $tasks
     * @param $date
     */
    public function __construct(EntityManagerInterface $entityManager, $date, $tasked)
    {
        $this->date = $date;
        $this->tasked = $tasked;
        $this->tasksRepository = $entityManager->getRepository(Tasks::class);
        $this->contractRepository = $entityManager->getRepository(Contract::class);
        $this->setToday();
        $this->loadCompletedTasks();

        if ($this->date >= $this->today) {
            $this->loadScheduledTasks();
            $this->loadContractTasks();
            $this->loadTasks();
        }
    }


    public function setToday()
    {
        $this->today = new \DateTime();
        $this->today->setTime(0, 0, 0);
        $this->isToday = ($this->today->format('Y-m-d') == $this->date->format('Y-m-d'));
        $this->dayName = $this->date->format('l');

    }

    public function loadCompletedTasks()
    {
        $completedTasks = $this->tasksRepository->getCompletedByDate($this->date);
        foreach ($completedTasks as $task) {
            $this->updateTasks($task);
        }
    }

    public function loadScheduledTasks()
    {
        $scheduledTasks = $this->tasksRepository->findBySchedule($this->date);
        foreach ($scheduledTasks as $scheduledTask) {
            $this->updateTasks($scheduledTask);
        }
    }

    public function loadContractTasks()
    {
        $contracts = $this->contractRepository->findBy(["isCompleted" => false]);
        foreach ($contracts as $contract) {
            $this->contractDayLength[$contract->getClient()->getId()] = $contract->getHoursPerDay() * 60;
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
}
