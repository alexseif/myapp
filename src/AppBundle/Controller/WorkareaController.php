<?php

namespace AppBundle\Controller;

use AppBundle\Logic\EarnedLogic;
use AppBundle\Repository\AccountsRepository;
use AppBundle\Repository\AccountTransactionsRepository;
use AppBundle\Repository\DaysRepository;
use AppBundle\Repository\TaskListsRepository;
use AppBundle\Repository\TasksRepository;
use AppBundle\Service\TasksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/workarea", name="workarea_")
 */
class WorkareaController extends AbstractController
{
    /**
     * @Route("/workarea", name="workarea")
     */
    public function workarea(
        TasksRepository $tasksRepository,
        DaysRepository $daysRepository,
        AccountsRepository $accountsRepository,
        AccountTransactionsRepository $accountTransactionsRepository,
        TaskListsRepository $taskListsRepository
    ): Response {
        $days = $daysRepository->getImportantCards();
        $accounts = $accountsRepository->findBy(['conceal' => false]);
        /** Cost Of Life * */
        $costOfLife = $this->get('myapp.cost');
        $earnedLogic = new EarnedLogic($this->getDoctrine()->getManager(), $costOfLife);
        $earned = $earnedLogic->getEarned();

        $issuedThisMonth = $accountTransactionsRepository->issuedThisMonth();
        $issued = 0;
        foreach ($issuedThisMonth as $tm) {
            $issued += abs($tm->getAmount());
        }
        $completedTodayCount = $tasksRepository->getCompletedTodayCount();

        return $this->render('workarea/workarea.html.twig', [
            'days' => $days,
            'accounts' => $accounts,
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $costOfLife,
            'issued' => $issued,
            'taskLists' => $taskListsRepository->findAllWithActiveTasks(),
            'completedTodayCount' => $completedTodayCount,
        ]);
    }

    /**
     * Get Inbox Tasks and render view.
     *
     * @param string $taskListName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasks/{taskListName}", name="get_tasks")
     */
    public function getTasksAction(TasksService $tasksService, $taskListName)
    {
        return $this->render('workarea/inboxTasks.html.twig', [
            'inboxTasks' => $tasksService->getWorkareaTasks($taskListName),
        ]);
    }

    /**
     * Get Inbox Tasks and render view.
     *
     * @param string $taskListName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasksCount/{taskListName}", name="get_tasks_count")
     */
    public function getTasksCountAction(
        TasksService $tasksService,
        $taskListName
    ) {
        return JsonResponse::create($tasksService->getWorkareaTasksCount($taskListName));
    }
}
