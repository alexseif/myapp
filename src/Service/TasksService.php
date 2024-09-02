<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\DashboardTaskLists;
use App\Entity\TaskLists;
use App\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of TasksService.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class TasksService
{
    protected $em;
    protected $tasksRepository;

    public function __construct(EntityManagerInterface $em, TasksRepository $tasksRepository)
    {
        $this->em = $em;
        $this->tasksRepository = $tasksRepository;
    }

    /**
     * @return TasksRepository
     */
    public function getRepository(): TasksRepository
    {
        return $this->tasksRepository;
    }

    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    public function getDashboardTasklists(): array
    {
        return $this->getEm()->getRepository(DashboardTaskLists::class)->findAll();
    }

    public function getWorkareaTasks($taskListName)
    {
        $inboxTasks = [];
        switch ($taskListName) {
            case 'focus':
                $inboxTasks = $this->getRepository()->focusLimitList();
                break;
            case 'urgent':
                $inboxTasks = $this->getRepository()->getUrgentTasks();
                break;
            case 'completedToday':
                $inboxTasks = $this->getRepository()->getCompletedToday();
                break;
            default:
                $taskList = $this->getEm()->getRepository(TaskLists::class)->findOneBy(['name' => $taskListName]);
                $inboxTasks = $this->getRepository()->focusByTasklist($taskList);
                break;
        }

        return $inboxTasks;
    }

    public function getWorkareaTasksCount($taskListName)
    {
        $inboxTasksCount = 0;
        switch ($taskListName) {
            case 'focus':
                $inboxTasksCount = 30;
                break;
            case 'urgent':
                $inboxTasksCount = count($this->getRepository()->getUrgentTasks());
                break;
            case 'completedToday':
                $inboxTasksCount = $this->getRepository()->getCompletedTodayCount();
                break;
            default:
                $taskList = $this->getRepository()->findOneBy(['name' => $taskListName]);
                $inboxTasksCount = count($this->getRepository()->focusByTasklist($taskList));
                break;
        }

        return $inboxTasksCount;
    }

    public function getRandom()
    {
        return $this->getRepository()->randomTasks();
    }

    public function getCompletedCountPerDayOfTheWeek(): array
    {
        $tsksCntDay = $this->getRepository()->findTasksCountByDay();
        $tskCnt = [];
        foreach ($tsksCntDay as $t) {
            $tskCnt[$t['day_name']] = $t['cnt'];
        }

        return $tskCnt;
    }

    public function getByUrgencyAndPriority()
    {
        return $this->getRepository()->countByUrgenctAndPriority();
    }

    public function getPieChartByUrgencyAndPriority(): array
    {
        $countByUrgenctAndPriority = $this->getByUrgencyAndPriority();
        $piechart = [];
        $piechart['Urgent & Important'] = 0;
        $piechart['Urgent'] = 0;
        $piechart['Important'] = 0;
        $piechart['Normal'] = 0;
        $piechart['Low'] = 0;
        foreach ($countByUrgenctAndPriority as $row) {
            $row['duration'] = (int) $row['duration'];
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
