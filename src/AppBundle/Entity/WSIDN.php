<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

/**
 * Description of WSIDN
 *
 * @author alexseif
 */
class WSIDN
{

    private $slots;
    private $total;
    private $timeIndex;
    private $max;

    function __construct()
    {
        $this->slots = array();
        $this->timeIndex = new \DateTime();
        $this->timeIndex->setTime('4', '0', '0');
        $this->total = 0;
        $this->max = 16 * 60;
    }

    function getTotal()
    {
        return $this->total;
    }

    function getMax()
    {
        return $this->max;
    }

    function setTotal($total)
    {
        $this->total = $total;
    }

    function setMax($max)
    {
        $this->max = $max;
    }

    function addSlot(Slot $slot)
    {
        if ($this->isSlotsAvailable()) {
            $this->total += $slot->getPeriod()->i;
            $slot->setTime(new \DateTime($this->timeIndex->format('H:i')));
            $this->slots[] = $slot;
            $this->timeIndex = $this->timeIndex->add($slot->getPeriod());
        }
        return $this;
    }

    function getSlots()
    {
        return $this->slots;
    }

    function setSlots($slots)
    {
        $this->slots = $slots;
    }

    public function getLastSlot()
    {
        return end($this->slots);
    }

    public function isSlotsAvailable()
    {
        return ($this->total < $this->max);
    }

    public function isLastSlot($name)
    {
        return (end($this->slots)->getName() == $name);
    }

}
