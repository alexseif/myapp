<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NotesRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotesRepository extends EntityRepository
{
    public function search($searchTerm)
    {
        return $this
            ->createQueryBuilder('n')
            ->select()
            ->where('n.note LIKE :searchTerm')
            ->setParameter(':searchTerm', '%'.$searchTerm.'%')
            ->getQuery()
            ->getResult();
    }
}
