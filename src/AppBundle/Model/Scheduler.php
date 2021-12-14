<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Model;


use AppBundle\Repository\TasksRepository;

class Scheduler
{
    public $dayLength;
    public $dayName;
    public $tasks;
    public $tasked = [];
    public $date, $today;
    public $tasksRepository;
    public $days;

    /**
     * @param $dayLength
     * @param $tasks
     * @param $date
     */
    public function __construct(TasksRepository $tasksRepository, $dayLength, $date, $tasked)
    {
        $this->today = new \DateTime();
        $this->today->setTime(0, 0, 0);
        $this->tasksRepository = $tasksRepository;
        $this->dayLength = $dayLength;
        $this->date = $date;
        $this->dayName = $this->date->format('l');
        $this->tasked = $tasked;

        $i = 0;
        $completedTasks = $this->tasksRepository->getCompletedByDate($this->date);
        foreach ($completedTasks as $task) {
            $this->tasks[] = $task;
            $this->dayLength -= $task->getDuration();
        }
        if ($this->date >= $this->today) {
            $focusTasks = $this->tasksRepository->focusListWithMeAndDateAndNotTasks($this->date, $this->tasked);
            $tasksCount = count($focusTasks);
            while ($i < $tasksCount) {
                if ($this->dayLength > 0) {
                    $this->dayLength -= ($focusTasks[$i]->getEst()) ?: 60;
                    $this->tasks[] = $focusTasks[$i];
                    $this->tasked[] = $focusTasks[$i]->getId();
                    $i++;
                } else {
                    break;
                }
            }
        }
    }

}
