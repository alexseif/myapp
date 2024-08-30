<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\Days;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of DaysService.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ReminderService
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return \Repository\DaysRepository
     */
    public function getRepository()
    {
        return $this->getEm()->getRepository(Days::class);
    }

    public function getActiveReminders()
    {
        return $this->getRepository()->getActiveCards();
    }
}
