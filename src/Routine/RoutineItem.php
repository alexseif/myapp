<?php

namespace App\Routine;

use DateInterval;
use DateTime;

class RoutineItem
{
    /**
     * @var string Routine Item name
     */
    protected $name;

    /**
     * @var DateTime Routine Item Time
     */
    protected $start;

    /**
     * @var DateInterval Routine Item duration
     */
    protected $duration;

    public function __construct(string $name, DateTime $start, DateInterval $duration)
    {
        $this->name = $name;
        $this->start = $start;
        $this->duration = $duration;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }

    public function setDuration(DateInterval $duration): void
    {
        $this->duration = $duration;
    }
}
