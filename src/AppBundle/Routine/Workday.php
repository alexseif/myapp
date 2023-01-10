<?php

namespace AppBundle\Routine;

use DateInterval;
use DateTime;

class Workday
{
    /**
     * @var DateTime
     */
    protected $dayStart;
    protected $runningTime;

    /**
     * @var array
     */
    protected $items = [];

    public function __construct(DateTime $dayStart = null)
    {
        $this->setDayStart((new DateTime())
            ->setTime(6, 15));
        if ($dayStart) {
            $this->setDayStart($dayStart);
        }
        $thirtyMinutes = '30 mins';
        $threeHours = '3 hours';

        $this->runningTime = clone $this->getDayStart();
        $this->addItem('Wake up', '0 mins');
        $this->addItem('Bathroom', $thirtyMinutes);
        $this->addItem('Kitchen', $thirtyMinutes);
        $this->addItem('Work', $threeHours);
        $this->addItem('Lunch', $thirtyMinutes);
        $this->addItem('Work', $threeHours);
        $this->addItem('Exercise/Snack', $thirtyMinutes);
        $this->addItem('Work', $threeHours);
        $this->addItem('Dance', $thirtyMinutes);
    }

    public function getDayStart(): DateTime
    {
        return $this->dayStart;
    }

    public function setDayStart(DateTime $dayStart): void
    {
        $this->dayStart = $dayStart;
    }

    /**
     * @param string $name
     * @param string $duration
     *
     * @return void
     */
    public function addItem(string $name, string $duration)
    {
        if (count($this->items)) {
            $this->items[] = new RoutineItem($name,
                clone $this->getRunningTime()->add(end($this->items)->getDuration()),
                DateInterval::createFromDateString($duration));
        } else {
            // First item exception
            $this->items[] = new RoutineItem($name, clone $this->getDayStart(),
                DateInterval::createFromDateString($duration));
        }
    }

    protected function getRunningTime(): DateTime
    {
        return $this->runningTime;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    protected function setRunningTime(DateTime $runningTime): void
    {
        $this->runningTime = $runningTime;
    }
}
