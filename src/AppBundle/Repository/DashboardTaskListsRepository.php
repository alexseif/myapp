<?php

namespace AppBundle\Repository;

/**
 * DashboardTaskListsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DashboardTaskListsRepository extends \Doctrine\ORM\EntityRepository
{

  public function findAllTaskLists()
  {
    return $this->getEntityManager()->createQuery("SELECT tl, dtl.id FROM AppBundle:TaskLists tl LEFT JOIN AppBundle:DashboardTaskLists dtl WITH dtl.taskList = tl.id  ")
            ->getResult();
  }

}
