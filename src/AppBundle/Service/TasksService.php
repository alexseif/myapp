<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Tasks;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of TasksService
 *
 * @author Alex Seif <me@alexseif.com>
 */
class TasksService
{

    protected $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     *
     * @return \AppBundle\Repository\TasksRepository
     */
    public function getRepository()
    {
        return $this->getEm()->getRepository('AppBundle:Tasks');
    }

    function getEm()
    {
        return $this->em;
    }

    public function getDashboardTasklists()
    {
        return $this->getEm()->getRepository('AppBundle:DashboardTaskLists')->findAll();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @deprecated since version number
     */
    public function getUnlisted()
    {
        return $this->getRepository()->findUnListed();
    }

    public function getRandom()
    {
        return $this->getRepository()->randomTasks();
    }

    public function getCompletedCountPerDayOfTheWeek()
    {
        $tsksCntDay = $this->getRepository()->findTasksCountByDay();
        $tskCnt = array();
        foreach ($tsksCntDay as $t) {
            $tskCnt[$t['day_name']] = $t['cnt'];
        }
        return $tskCnt;
    }

    public function getByUrgencyAndPriority()
    {
        return $this->getRepository()->countByUrgenctAndPriority();
    }

    public function getPieChartByUrgencyAndPriority()
    {
        $countByUrgenctAndPriority = $this->getByUrgencyAndPriority();
        $piechart = [];
        $piechart['Urgent & Important'] = 0;
        $piechart['Urgent'] = 0;
        $piechart['Important'] = 0;
        $piechart['Normal'] = 0;
        $piechart['Low'] = 0;
        foreach ($countByUrgenctAndPriority as $key => $row) {
            $row['duration'] = (int)$row['duration'];
            if ($row['urgency']) {
                if ($row['priority']) {
                    $piechart['Urgent & Important'] = $row['duration'];
                } else {
                    $piechart['Urgent'] = $row['duration'];
                }
            } elseif ($row['priority'] > 0) {
                $piechart['Important'] = $row['duration'];
            } elseif ($row['priority'] < 0) {
                $piechart['Low'] = $row['duration'];
            } else {
                $piechart['Normal'] = $row['duration'];
            }
        }
        return $piechart;
    }

}
