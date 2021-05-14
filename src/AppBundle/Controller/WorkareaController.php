<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Logic\EarnedLogic;
use AppBundle\Repository\AccountsRepository;
use AppBundle\Repository\AccountTransactionsRepository;
use AppBundle\Repository\DaysRepository;
use AppBundle\Repository\TaskListsRepository;
use AppBundle\Repository\TasksRepository;
use AppBundle\Service\FocusService;
use AppBundle\Service\TasksService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/workarea", name="workarea_")
 */
class WorkareaController extends Controller
{
    /**
     * @Route("/workarea", name="workarea")
     *
     * @param TasksRepository $tasksRepository
     * @param DaysRepository $daysRepository
     * @param AccountsRepository $accountsRepository
     * @param AccountTransactionsRepository $accountTransactionsRepository
     * @return Response
     */
    public function workarea(TasksRepository $tasksRepository, DaysRepository $daysRepository, AccountsRepository $accountsRepository, AccountTransactionsRepository $accountTransactionsRepository, TaskListsRepository $taskListsRepository): Response
    {
        $days = $daysRepository->getImportantCards();
        $accounts = $accountsRepository->findBy(array('conceal' => false));
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
            'completedTodayCount' => $completedTodayCount

        ]);
    }


    /**
     * Get Inbox Tasks and render view
     * @param string $taskListName
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasks/{taskListName}", name="get_tasks")
     */
    public function getTasksAction(TasksService $tasksService, $taskListName)
    {
        return $this->render("AppBundle:inbox:inboxTasks.html.twig", [
            'inboxTasks' => $tasksService->getWorkareaTasks($taskListName)
        ]);
    }


    /**
     *
     * @Route("/focus", name="focus")
     */
    public function focusAction(Request $request, FocusService $focusService): ?Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('client')) {
            $client = $em->getRepository('AppBundle:Client')->find($request->get('client'));
            if (empty($client)) {
                throw new NotFoundHttpException("Client not found");
            }
            $focusService->setTasks($client);
        } else {
            $focusService->setTasks();
        }

        return $this->render("workarea/focus.html.twig", $focusService->get());
    }

    /**
     *
     * @Route("/focus/{name}", name="focus_tasklist")
     * @param TaskLists $tasklist
     * @param FocusService $focusService
     * @return Response|null
     */
    public function focusByTaskListAction(TaskLists $tasklist, FocusService $focusService): ?Response
    {
        $focusService->setTasksByTaskList($tasklist);

        return $this->render("workarea/focus.html.twig", $focusService->get());
    }
}
