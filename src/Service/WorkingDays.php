<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\Holiday;
use App\Util\DateRanges;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service to calculate working days.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class WorkingDays
{

    private static array $workWeek = [
      'Friday' => 0,
      'Saturday' => 4,
      'Sunday' => 8,
      'Monday' => 8,
      'Tuesday' => 8,
      'Wednesday' => 8,
      'Thursday' => 8,
    ];

    /**
     * @var EntityManager
     */
    protected EntityManagerInterface|EntityManager $em;

    /**
     * @var CostService
     */
    protected CostService $cs;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getWorkWeek(): array
    {
        return self::$workWeek;
    }

    public static function getDayHours($day): ?int
    {
        return self::$workWeek[$day] ?? null;
    }

    public function updateHolidays(): void
    {
        $curlHolidays = DateRanges::getHolidays('egypt');
        $holidays = [];
        foreach ($curlHolidays as $curlHoliday) {
            $dt = new DateTime($curlHoliday[0]);
            $dateKey = $dt->format('Y-m-d');
            if (array_key_exists($dateKey, $holidays)) {
                $holidays[$dateKey]->setName(
                  $holidays[$dateKey]->getName() . ', ' . $curlHoliday[2]
                );
                $holidays[$dateKey]->setType(
                  $holidays[$dateKey]->getType() . ', ' . $curlHoliday[3]
                );
                continue;
            }
            $holiday = $this->em->getRepository(Holiday::class)->findOneBy(
              ['date' => $dt]
            );
            $holidays[$dateKey] = ($holiday) ?: new Holiday();
            $holidays[$dateKey]->setDate($dt);
            $holidays[$dateKey]->setName($curlHoliday[2]);
            $holidays[$dateKey]->setType($curlHoliday[3]);
            $this->em->persist($holidays[$dateKey]);
        }
        $this->em->flush();
    }

}
