<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

/**
 * Description of Slot
 *
 * @author alexseif
 */
class Slot
{

    private $name;
    private $period;
    private $time;

    function __construct($name, $period)
    {
        $this->name = $name;
        $this->setPeriod($period);
    }

    function getName()
    {
        return $this->name;
    }

    function getPeriod()
    {
        return $this->period;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setPeriod($period)
    {
        $this->period = new \DateInterval('PT' . $period . 'M');
    }

    function getTime()
    {
        return $this->time;
    }

    function setTime($time)
    {
        $this->time = $time;
    }

}
