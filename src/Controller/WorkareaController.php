<?php

namespace App\Controller;

use App\Repository\AccountsRepository;
use App\Repository\AccountTransactionsRepository;
use App\Repository\DaysRepository;
use App\Repository\TaskListsRepository;
use App\Repository\TasksRepository;
use App\Service\CostService;
use App\Service\TasksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/workarea", name:"workarea_")]
class WorkareaController extends AbstractController
{

    #[Route("/workarea", name:"workarea")]
    public function workarea(
      TasksRepository $tasksRepository,
      DaysRepository $daysRepository,
      AccountsRepository $accountsRepository,
      AccountTransactionsRepository $accountTransactionsRepository,
      TaskListsRepository $taskListsRepository,
      CostService $costService
    ): Response {
        $days = $daysRepository->getImportantCards();
        //        $accounts = $accountsRepository->findBy(['conceal' => false]);
        /** Cost Of Life * */
        //        $costOfLife = $costService;
        //        $earnedLogic = new EarnedLogic($this->getDoctrine()->getManager(), $costOfLife);
        //        $earned = $earnedLogic->getEarned();
        //
        //        $issuedThisMonth = $accountTransactionsRepository->issuedThisMonth();
        //        $issued = 0;
        //        foreach ($issuedThisMonth as $tm) {
        //            $issued += abs($tm->getAmount());
        //        }
        $completedTodayCount = $tasksRepository->getCompletedTodayCount();

        return $this->render('workarea/workarea.html.twig', [
          'days' => $days,
            //            'accounts' => $accounts,
            //            'earned' => $earned,
            //            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            //            'costOfLife' => $costOfLife,
            //            'issued' => $issued,
          'taskLists' => $taskListsRepository->findAllWithActiveTasks(),
          'completedTodayCount' => $completedTodayCount,
        ]);
    }

    /**
     * Get Inbox Tasks and render view.
     *
     *
     *
     * @Route("/getTasks/{taskListName}", name="get_tasks")
     */
    public function getTasksAction(
      TasksService $tasksService,
      string $taskListName
    ): Response {
        return $this->render('workarea/inboxTasks.html.twig', [
          'inboxTasks' => $tasksService->getWorkareaTasks($taskListName),
        ]);
    }

    /**
     * Get Inbox Tasks and render view.
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasksCount/{taskListName}", name="get_tasks_count")
     */
    public function getTasksCountAction(
      TasksService $tasksService,
      string $taskListName
    ) {
        return new JsonResponse(
          $tasksService->getWorkareaTasksCount($taskListName)
        );
    }

}
