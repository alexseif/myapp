<?php

namespace AppBundle\Controller;

use AppBundle\Logic\EarnedLogic;
use AppBundle\Repository\AccountsRepository;
use AppBundle\Repository\AccountTransactionsRepository;
use AppBundle\Repository\DaysRepository;
use AppBundle\Repository\HolidayRepository;
use AppBundle\Repository\TaskListsRepository;
use AppBundle\Repository\TasksRepository;
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
    public function workarea(TasksRepository $tasksRepository, DaysRepository $daysRepository, AccountsRepository $accountsRepository, AccountTransactionsRepository $accountTransactionsRepository, TaskListsRepository $taskListsRepository): Response
    {
        $focusTasks = $tasksRepository->focusLimitList();
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

        return $this->render('workarea/workarea.html.twig', [
            'focus' => $focusTasks,
            'days' => $days,
            'accounts' => $accounts,
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $costOfLife,
            'issued' => $issued,
            'taskLists' => $taskListsRepository->findAllWithActiveTasks()

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
     * @todo refactor to service
     * @param string $taskListName
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasks/{taskListName}", name="get_tasks")
     */
    public function getTasksAction(TasksRepository $tasksRepository, TaskListsRepository $taskListsRepository, $taskListName)
    {
        $inboxTasks = [];
        switch ($taskListName) {
            case 'focus':
                $inboxTasks = $tasksRepository->focusLimitList();
                break;
            case 'urgent':
                break;
            case 'completedToday':
                $inboxTasks = $tasksRepository->getCompletedToday();

                break;
            default:
                // @TODO: Not found handler
                $taskList = $taskListsRepository->findOneBy(['name' => $taskListName]);
                $inboxTasks = $tasksRepository->focusByTasklist($taskList);
                break;
        }
        return $this->render("AppBundle:inbox:inboxTasks.html.twig", [
            'inboxTasks' => $inboxTasks
        ]);
    }
}
