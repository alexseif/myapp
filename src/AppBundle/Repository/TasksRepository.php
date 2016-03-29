<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TasksRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TasksRepository extends EntityRepository
{

    public function findUnListed()
    {
        return $this->findBy(array("taskList" => null));
    }

}
