<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Service to calculate working days
 *
 * @author Alex Seif <me@alexseif.com>
 */
class WorkingDays
{

    private static $workWeek = [
        'Friday' => 0,
        'Saturday' => 4,
        'Sunday' => 8,
        'Monday' => 8,
        'Tuesday' => 8,
        'Wednesday' => 8,
        'Thursday' => 8,
    ];

    /**
     *
     * @var EntityManager $em
     */
    protected $em;

    /**
     *
     * @var CostService $cs
     */
    protected $cs;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getWorkWeek()
    {
        return self::$workWeek;
    }

    public static function getDayHours($day)
    {
        if (key_exists($day, self::$workWeek)) {
            return self::$workWeek[$day];
        }
        return null;
    }

    public function updateHolidays()
    {
        $curlHolidays = \AppBundle\Util\DateRanges::getHolidays("egypt");
        foreach ($curlHolidays as $curlHoliday) {
            $dt = new \DateTime($curlHoliday[0]);
            $holiday = $this->em->getRepository('AppBundle:Holiday')->findBy(['date' => $dt]);
            if (!$holiday) {
                $holiday = new \AppBundle\Entity\Holiday();
                $holiday->setDate($dt);
                $holiday->setName($curlHoliday[2]);
                $holiday->setType($curlHoliday[3]);
                $this->em->persist($holiday);
            }
        }
        $this->em->flush();
    }

}
