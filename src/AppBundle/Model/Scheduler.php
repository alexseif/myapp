<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Model;


use AppBundle\Repository\TasksRepository;

class Scheduler
{
    public $dayLength = 360;
    public $iSoftLength = 120;
    public $dayName;
    public $tasks;
    public $tasked = [];
    public $date, $today;
    public $tasksRepository;

    /**
     * @param $dayLength
     * @param $tasks
     * @param $date
     */
    public function __construct(TasksRepository $tasksRepository, $date, $tasked)
    {
        $this->date = $date;
        $this->tasked = $tasked;
        $this->tasksRepository = $tasksRepository;

        $this->setToday();
        $this->loadCompletedTasks();

        if ($this->date >= $this->today) {
            $this->loadIsoftTasks();
            $this->loadTasks();
        }
    }


    public function setToday()
    {
        $this->today = new \DateTime();
        $this->today->setTime(0, 0, 0);
        $this->dayName = $this->date->format('l');
    }

    public function loadCompletedTasks()
    {
        $completedTasks = $this->tasksRepository->getCompletedByDate($this->date);
        foreach ($completedTasks as $task) {
            $this->tasks[] = $task;
            $this->dayLength -= $task->getDuration();
            if (30 == $task->getClient()->getId()) {
                $this->iSoftLength -= $task->getDuration();
            }
        }
    }

    //@todo: this is bad it's locked to a client
    public function loadIsoftTasks()
    {
        $iSoftTasks = $this->tasksRepository->focusListWithMeAndDateAndNotTasksiSoft($this->date, $this->tasked);
        foreach ($iSoftTasks as $task) {
            if ($this->iSoftLength <= 0) {
                break;
            }
            $this->iSoftLength -= ($task->getEst()) ?: 60;
            $this->tasks[] = $task;
            $this->tasked[] = $task->getId();
        }
    }

    public function loadTasks()
    {
        $focusTasks = $this->tasksRepository->focusListWithMeAndDateAndNotTasks($this->date, $this->tasked);
        foreach ($focusTasks as $task) {
            if ($this->dayLength <= 0) {
                break;
            }
            $this->dayLength -= ($task->getEst()) ?: 60;
            $this->tasks[] = $task;
            $this->tasked[] = $task->getId();
        }

    }
}
