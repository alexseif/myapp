<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AccountsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountsRepository extends EntityRepository
{

  public function search($searchTerm)
  {
    return $this
            ->createQueryBuilder('a')
            ->select()
            ->where('a.name LIKE :searchTerm')
            ->setParameter(":searchTerm", '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
  }

  public function findAllwithJoin()
  {

    return $this
            ->createQueryBuilder('a')
            ->select('a, c')
            ->leftJoin('a.client', 'c');
  }

}
