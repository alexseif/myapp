<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Holiday;
use AppBundle\Logic\EarnedLogic;
use AppBundle\Service\AccountsService;
use AppBundle\Service\ReminderService;
use AppBundle\Service\TasksService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboardAction(
      TasksService $ts,
      ReminderService $rs,
      AccountsService $as
    ) {
        $em = $this->getDoctrine()->getManager();

        $earnedLogic = new EarnedLogic($em, $this->get('myapp.cost'));
        $accounts = $as->getDashboard();
        return $this->render('dashboard/dashboard.html.twig', [
          'taskLists' => $ts->getDashboardTasklists(),
          'randomTasks' => $ts->getRandom(),
          'days' => $rs->getActiveReminders(),
          'holidays' => $em->getRepository(Holiday::class)->getComingHolidays(),
          'accounts' => $accounts,
          'earned' => $earnedLogic->getEarned(),
          'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
          'tskCnt' => $ts->getCompletedCountPerDayOfTheWeek(),
          'costOfLife' => $this->get('myapp.cost'),
          'piechart' => $ts->getPieChartByUrgencyAndPriority(),
        ]);
    }

    /**
     * @Route("/elements", name="elements")
     */
    public function elementsAction()
    {
        return $this->render('dashboard/elements.html.twig');
    }

    /**
     * @Route("/workspace", name="workspace")
     */
    public function workspaceAction()
    {
        return $this->render('dashboard/workspace.html.twig');
    }

    /**
     * @Route("/remind", name="remind")
     */
    public function remindAction()
    {
        return $this->render('dashboard/remindMe.html.twig');
    }

    /**
     * @Route("/empty", name="empty")
     */
    public function emptyAction()
    {
        return $this->render('empty.html.twig');
    }

}
