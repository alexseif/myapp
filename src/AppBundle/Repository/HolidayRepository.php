<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * HolidayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HolidayRepository extends EntityRepository
{

  public function getComingHolidays()
  {
    return $this->createQueryBuilder('h')
            ->where('h.date >= CURRENT_TIMESTAMP()')
            ->orderBy('h.date', 'ASC')
            ->getQuery()
            ->getResult();
  }

  public function findByRange($from, $to)
  {
    return $this->createQueryBuilder('h')
            ->where('h.date BETWEEN :from AND :to')
            ->setParameter(":from", $from->format('Y-m-d'))
            ->setParameter(":to", $to->format('Y-m-d'))
            ->orderBy('h.date', 'ASC')
            ->getQuery()
            ->getResult();
  }

}