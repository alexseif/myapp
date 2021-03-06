<?php

namespace AppBundle\Controller;

use AppBundle\Logic\EarnedLogic;
use AppBundle\Repository\AccountsRepository;
use AppBundle\Repository\AccountTransactionsRepository;
use AppBundle\Repository\DaysRepository;
use AppBundle\Repository\HolidayRepository;
use AppBundle\Repository\ScenarioDetailsRepository;
use AppBundle\Repository\ScenarioObjectiveRepository;
use AppBundle\Repository\TaskListsRepository;
use AppBundle\Repository\TasksRepository;
use AppBundle\Service\TasksService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
    public function workarea(TasksRepository $tasksRepository, DaysRepository $daysRepository, AccountsRepository $accountsRepository, AccountTransactionsRepository $accountTransactionsRepository, TaskListsRepository $taskListsRepository, ScenarioDetailsRepository $scenarioDetailsRepository, ScenarioObjectiveRepository $scenarioObjectiveRepository): Response
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
//        @todo move to js load from scenario controller
        $scenarioDetails = $scenarioDetailsRepository->getToday();
        $scenarioObjectives = $scenarioObjectiveRepository->getToday();
        if (1 > count($scenarioObjectives)) {
            $scenarioObjectives = $scenarioObjectiveRepository->getAnObjective();
        }
        return $this->render('workarea/workarea.html.twig', [
            'days' => $days,
            'accounts' => $accounts,
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $costOfLife,
            'issued' => $issued,
            'taskLists' => $taskListsRepository->findAllWithActiveTasks(),
            'completedTodayCount' => $completedTodayCount,
            'scenario_details' => $scenarioDetails,
            'scenario_objectives' => $scenarioObjectives,

        ]);
    }

    /**
     *
     * @Route("/inbox", name="inbox")
     */
    public function inboxAction(TaskListsRepository $taskListsRepository, DaysRepository $daysRepository, HolidayRepository $holidayRepository)
    {
        $taskLists = $taskListsRepository->findAllWithActiveTasks();
        $days = $daysRepository->getActiveCards();
        $holidays = $holidayRepository->getComingHolidays();

        $urgentTasks = [];
        return $this->render("workarea/inbox.html.twig", [
            'days' => $days,
            'holidays' => $holidays,
            'taskLists' => $taskLists,
            'urgentTasks' => $urgentTasks,
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
}
