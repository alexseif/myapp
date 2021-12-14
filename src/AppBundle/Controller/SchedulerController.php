<?php

namespace AppBundle\Controller;

use AppBundle\Model\Scheduler;
use AppBundle\Repository\TasksRepository;
use AppBundle\Service\FocusService;
use AppBundle\Util\DateRanges;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scheduler")
 */
class SchedulerController extends AbstractController
{
    function getPeriod($year, $week)
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $dto->modify('-1 days');
        $week_start = clone $dto;
        $dto->modify('+5 days');
        $week_end = clone $dto;
        $interval = \DateInterval::createFromDateString('1 day');
        return new \DatePeriod($week_start, $interval, $week_end);
    }

    /**
     * @Route("/{year}/{week}", name="scheduler")
     */
    public function index(TasksRepository $tasksRepository, $year, $week): Response
    {
        //@todo: scheduler service to populate scheduling items
        //@todo: calendar -> view -> week
        //@todo: calendar to view schedule
        //@todo: Scheduler to schedule items in calendar

        $period = $this->getPeriod(date('Y'), $week);
        $schedulers = [];
        $tasked = [];
        foreach ($period as $dt) {
            $scheduler = new Scheduler($tasksRepository, 8 * 60, $dt, $tasked);
            $tasked += $scheduler->tasked;
            $schedulers[] = $scheduler;
        }
        return $this->render('scheduler/index.html.twig', [
            'week' => $week,
            'year' => $year,
            'schedulers' => $schedulers
        ]);
    }
}
