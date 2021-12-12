<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of TasksService
 *
 * @author Alex Seif <me@alexseif.com>
 */
class TasksService
{

    protected $em;
    protected $tasksRepository;

    function __construct(EntityManagerInterface $em, TasksRepository $tasksRepository)
    {
        $this->em = $em;
        $this->tasksRepository = $tasksRepository;
    }

    /**
     *
     * @return \AppBundle\Repository\TasksRepository
     */
    public function getRepository()
    {
        return $this->tasksRepository;
    }

    function getEm()
    {
        return $this->em;
    }

    public function getDashboardTasklists()
    {
        return $this->getEm()->getRepository('AppBundle:DashboardTaskLists')->findAll();
    }

    public function getWorkareaTasks($taskListName)
    {
        $inboxTasks = [];
        switch ($taskListName) {
            case 'focus':
                $inboxTasks = $this->getRepository()->focusLimitList();
                break;
            case 'urgent':
                break;
            case 'completedToday':
                $inboxTasks = $this->getRepository()->getCompletedToday();
                break;
            default:
                $inboxTasks = $this->getFocusByTaskListName($taskListName);
                break;
        }
        return $inboxTasks;
    }

    public function getFocusByTaskListName($taskListName)
    {
        // @TODO: Not found handler
        $taskList = $this->getEm()->getRepository(TaskLists::class)->findOneBy(['name' => $taskListName]);
        return $this->getRepository()->focusByTasklist($taskList);
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
        foreach ($countByUrgenctAndPriority as $row) {
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
