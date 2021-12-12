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
        $focusTasks = $tasksRepository->focusList(30);
        $tasksCount = count($focusTasks);
        $week_days = DateRanges::getWorkWeek();
        $i = 0;
        foreach ($week_days as $dayKey => $week_day) {
            $focus['days'][$dayKey] = [];
            $dayLength = 8 * 60;
            while ($i < $tasksCount) {
                if ($dayLength > 0) {
                    $dayLength -= ($focusTasks[$i]->getEst()) ?: 60;
                    $focus['days'][$dayKey][] = $focusTasks[$i];
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
