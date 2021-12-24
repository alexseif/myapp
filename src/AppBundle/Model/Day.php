<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace AppBundle\Model;

use AppBundle\Entity\Tasks;
use Doctrine\Common\Collections\ArrayCollection;

class Day
{
    /**
     * @var ArrayCollection
     */
    public $schedules;
    public $tasks;

    /**
     * @var \DateTime
     */
    public $date;
    public $today;

    /**
     * @var bool
     */
    public $isToday;
    public $dayLength = 540;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

    public function getSchedules(): ArrayCollection
    {
        return $this->schedules;
    }

    public function setSchedules(ArrayCollection $schedules): void
    {
        $this->schedules = $schedules;
    }

    public function addTask(Tasks $task)
    {
        if ($this->dayLength > 0) {
            $this->tasks->add($task);
            if ($task->getCompleted()) {
                $mins = $task->getDuration();
            } else {
                $mins = ($task->getEst()) ?: 60;
            }
            $this->dayLength -= $mins;

            return true;
        }

        return false;
    }

    public function getTasks(): ArrayCollection
    {
        return $this->tasks;
    }

    public function setTasks(ArrayCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getToday(): \DateTime
    {
        return $this->today;
    }

    public function setToday(\DateTime $today): void
    {
        $this->today = $today;
    }

    public function isToday(): bool
    {
        return $this->isToday;
    }

    public function setIsToday(bool $isToday): void
    {
        $this->isToday = $isToday;
    }

    public function getDayLength(): int
    {
        return $this->dayLength;
    }

    public function setDayLength(int $dayLength): void
    {
        $this->dayLength = $dayLength;
    }
}
