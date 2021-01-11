<?php

namespace AppBundle\Entity;

use AppBundle\Repository\HolidayRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Holiday
 *
 * @ORM\Entity(repositoryClass=HolidayRepository::class)
 * @UniqueEntity("date")
 */
class Holiday
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    private $date;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    function getType()
    {
        return $this->type;
    }

    function setType($type)
    {
        $this->type = $type;
    }

}
