<?php

namespace AppBundle\Controller;

use AppBundle\Repository\TasksRepository;
use AppBundle\Service\FocusService;
use AppBundle\Util\DateRanges;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchedulerController extends AbstractController
{
    /**
     * @Route("/scheduler", name="scheduler")
     */
    public function index(TasksRepository $tasksRepository): Response
    {
        $tasked = [];
        $date = new \DateTime();
        $focusTasks = $tasksRepository->focusListWithMeAndDateAndNotTasks($date, $tasked);
        $tasksCount = count($focusTasks);
        $week_days = DateRanges::getWorkWeek();
        $day = date('w');
        $week_start = new \DateTime(date('Y-m-d', strtotime('-' . $day . ' days')));
        $week_end = new \DateTime(date('Y-m-d', strtotime('+' . (5 - $day) . ' days')));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($week_start, $interval, $week_end);
        $focus = [];
        $i = 0;
        foreach ($period as $dt) {
            $dayLength = 8 * 60;
            $completedTasks = $tasksRepository->getCompletedByDate($dt);
            $focus[$dt->format('l')] = [];
            foreach ($completedTasks as $task) {
                $focus[$dt->format('l')][] = $task;
                $dayLength -= $task->getDuration();
            }
            $focusTasks = $tasksRepository->focusListWithMeAndDateAndNotTasks($dt, $tasked);
            while ($i < $tasksCount) {
                if ($dayLength > 0) {
                    $dayLength -= ($focusTasks[$i]->getEst()) ?: 60;
                    $focus[$dt->format('l')][] = $focusTasks[$i];
                    $tasked[] = $focusTasks[$i]->getId();
                    $i++;
                } else {
                    break;
                }
            }
        }

        return $this->render('scheduler/index.html.twig', [
            'week_days' => $week_days,
            'focus' => $focus
        ]);
    }
}
