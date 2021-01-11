<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Recurr\Recurrence;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Recurr
 *
 * @ORM\Table(name="recurr")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecurrRepository")
 */
class RecurrEntity
{

    use TimestampableEntity;

    private static $frequencyList = array(0 => 'DAILY', 1 => 'WEEKLY', 2 => 'MONTHLY', 3 => 'YEARLY');
    private static $weekDays = array(0 => 'SU', 1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA');

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime - indicates 'first date' of recurring timeframe
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateStart;

    /**
     * @var \DateTime - indicates 'until date' of rRule
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateUntil;

    /**
     * @var int - indicates 'how often' rRule applies
     * @ORM\Column(type="integer", nullable=true)
     */
    private $count;

    /**
     * @var int - indicates 'how often' per frequency, ex. 1 means every month, 2 every other month
     * @ORM\Column(type="integer", nullable=true, name="freq_interval")
     */
    private $interval;

    /**
     * @var int - indicates 'frequency', uses self::$frequencyList
     * @ORM\Column(type="integer", nullable=true)
     */
    private $freq;

    /**
     * @var array - indicates 'every day', uses self::$weekDays
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $byDay;

    /**
     * @var array - integer indicates 'day of month', e.g. 1 is first day of month, -1 is last day
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $byMonthDay;

    /**
     * @var array - integer indicates 'day of year', e.g. 1 is first day of year, -1 is last day of year
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $byYearDay;

    /**
     * @var array - indicates 'every month' January is 0, December = 12
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $byMonth;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Recurr
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateStart.
     *
     * @param \DateTime|null $dateStart
     *
     * @return Recurr
     */
    public function setDateStart($dateStart = null)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart.
     *
     * @return \DateTime|null
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateUntil.
     *
     * @param \DateTime|null $dateUntil
     *
     * @return Recurr
     */
    public function setDateUntil($dateUntil = null)
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    /**
     * Get dateUntil.
     *
     * @return \DateTime|null
     */
    public function getDateUntil()
    {
        return $this->dateUntil;
    }

    /**
     * Set count.
     *
     * @param int|null $count
     *
     * @return Recurr
     */
    public function setCount($count = null)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count.
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set interval.
     *
     * @param int|null $interval
     *
     * @return Recurr
     */
    public function setInterval($interval = null)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get interval.
     *
     * @return int|null
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set freq.
     *
     * @param int|null $freq
     *
     * @return Recurr
     */
    public function setFreq($freq = null)
    {
        $this->freq = $freq;

        return $this;
    }

    /**
     * Get freq.
     *
     * @return int|null
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * Set byDay.
     *
     * @param array|null $byDay
     *
     * @return Recurr
     */
    public function setByDay($byDay = null)
    {
        $this->byDay = $byDay;

        return $this;
    }

    /**
     * Get byDay.
     *
     * @return array|null
     */
    public function getByDay()
    {
        return $this->byDay;
    }

    /**
     * Set byMonthDay.
     *
     * @param array|null $byMonthDay
     *
     * @return Recurr
     */
    public function setByMonthDay($byMonthDay = null)
    {
        $this->byMonthDay = $byMonthDay;

        return $this;
    }

    /**
     * Get byMonthDay.
     *
     * @return array|null
     */
    public function getByMonthDay()
    {
        return $this->byMonthDay;
    }

    /**
     * Set byYearDay.
     *
     * @param array|null $byYearDay
     *
     * @return Recurr
     */
    public function setByYearDay($byYearDay = null)
    {
        $this->byYearDay = $byYearDay;

        return $this;
    }

    /**
     * Get byYearDay.
     *
     * @return array|null
     */
    public function getByYearDay()
    {
        return $this->byYearDay;
    }

    /**
     * Set byMonth.
     *
     * @param array|null $byMonth
     *
     * @return Recurr
     */
    public function setByMonth($byMonth = null)
    {
        $this->byMonth = $byMonth;

        return $this;
    }

    /**
     * Get byMonth.
     *
     * @return array|null
     */
    public function getByMonth()
    {
        return $this->byMonth;
    }

    /**
     * @return null|\DateTime[]
     */
    public function getRecurrenceCollection()
    {

//    if ($this->getDateEffective() !== null) {
//      return null;
//    }

        if ($this->getFreq() === null) {
            return null;
        }

        $result = array();

        if ($this->getFreq() !== null) {
            $rulePart[] = 'FREQ=' . self::$frequencyList[$this->getFreq()];
        }

        if ($this->getCount() !== null) {
            $rulePart[] = 'COUNT=' . $this->getCount();
        }

        if ($this->getDateUntil() !== null) {
            $rulePart[] = 'UNTIL=' . $this->getDateUntil()->format('c');
        }

        if ($this->getInterval() === null) {
            $rulePart[] = 'INTERVAL=1';
        } else {
            $rulePart[] = 'INTERVAL=' . $this->getInterval();
        }
        if (count($this->getByDay()) > 0) {
            $byWeekDayArray = array();
            foreach ($this->getByDay() as $byDay) {
                $byWeekDayArray[] = self::$weekDays[$byDay];
            }

            $rulePart[] = 'BYDAY=' . implode(',', $byWeekDayArray);
        }
        if (count($this->getByMonth()) > 0) {
            $rulePart[] = 'BYMONTH=' . implode(',', $this->getByMonth());
        }
        if (count($this->getByMonthDay()) > 0) {
            $rulePart[] = 'BYMONTHDAY=' . implode(',', $this->getByMonthDay());
        }
        if (count($this->getByYearDay()) > 0) {
            $rulePart[] = 'BYYEARDAY=' . implode(',', $this->getByYearDay());
        }

        $ruleString = implode(';', $rulePart);
        $rule = new Rule($ruleString, $this->getDateStart()->format('c'), null, 'UTC');

        $transformer = new ArrayTransformer();

        // enable fix for MONTHLY
        if ($this->getFreq() === 2) {
            $transformerConfig = new ArrayTransformerConfig();
            $transformerConfig->enableLastDayOfMonthFix();
            $transformer->setConfig($transformerConfig);
        }

        $elements = $transformer->transform($rule);

        /** @var Recurrence $element */
        foreach ($elements as $element) {
            $result[] = $element->getEnd();
        }
        return $result;
    }

}
