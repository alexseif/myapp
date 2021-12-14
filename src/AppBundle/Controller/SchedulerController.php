<?php

namespace AppBundle\Controller;

use AppBundle\Model\Scheduler;
use AppBundle\Repository\TasksRepository;
use AppBundle\Service\FocusService;
use AppBundle\Util\DateRanges;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scheduler")
 */
class SchedulerController extends AbstractController
{
    function getPeriod()
    {
        $day = date('w');
        $week_start = new \DateTime(date('Y-m-d', strtotime('-' . $day . ' days')));
        $week_end = new \DateTime(date('Y-m-d', strtotime('+' . (5 - $day) . ' days')));
        $interval = \DateInterval::createFromDateString('1 day');
        return new \DatePeriod($week_start, $interval, $week_end);
    }

    /**
     * @Route("/", name="scheduler")
     */
    public function index(TasksRepository $tasksRepository): Response
    {
        //@todo: scheduler service to populate scheduling items
        //@todo: calendar -> view -> week
        //@todo: calendar to view schedule
        //@todo: Scheduler to schedule items in calendar

        $period = $this->getPeriod();
        $schedulers = [];
        $tasked = [];
        foreach ($period as $dt) {
            $scheduler = new Scheduler($tasksRepository, 8 * 60, $dt, $tasked);
            $tasked += $scheduler->tasked;
            $schedulers[] = $scheduler;
        }

        return $this->render('scheduler/index.html.twig', [
            'schedulers' => $schedulers
        ]);
    }
}
