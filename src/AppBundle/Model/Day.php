<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Model;

use AppBundle\Entity\Tasks;
use Doctrine\Common\Collections\ArrayCollection;

class Day
{
    /**
     * @var ArrayCollection
     */
    public $schedules, $tasks;

    /**
     * @var \DateTime
     */
    public $date, $today;

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


    /**
     * @return ArrayCollection
     */
    public function getSchedules(): ArrayCollection
    {
        return $this->schedules;
    }

    /**
     * @param ArrayCollection $schedules
     */
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

    /**
     * @return ArrayCollection
     */
    public function getTasks(): ArrayCollection
    {
        return $this->tasks;
    }

    /**
     * @param ArrayCollection $tasks
     */
    public function setTasks(ArrayCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getToday(): \DateTime
    {
        return $this->today;
    }

    /**
     * @param \DateTime $today
     */
    public function setToday(\DateTime $today): void
    {
        $this->today = $today;
    }

    /**
     * @return bool
     */
    public function isToday(): bool
    {
        return $this->isToday;
    }

    /**
     * @param bool $isToday
     */
    public function setIsToday(bool $isToday): void
    {
        $this->isToday = $isToday;
    }

    /**
     * @return int
     */
    public function getDayLength(): int
    {
        return $this->dayLength;
    }

    /**
     * @param int $dayLength
     */
    public function setDayLength(int $dayLength): void
    {
        $this->dayLength = $dayLength;
    }


}