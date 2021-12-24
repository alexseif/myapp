<?php

namespace AppBundle\Controller;

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
    public function dashboardAction(TasksService $ts, ReminderService $rs, AccountsService $as)
    {
        $em = $this->getDoctrine()->getManager();

        $earnedLogic = new EarnedLogic($em, $this->get('myapp.cost'));

        return $this->render('AppBundle:Dashboard:dashboard.html.twig', [
            'taskLists' => $ts->getDashboardTasklists(),
            'randomTasks' => $ts->getRandom(),
            'unlistedTasks' => $ts->getUnlisted(),
            'days' => $rs->getActiveReminders(),
            'holidays' => $em->getRepository('AppBundle:Holiday')->getComingHolidays(),
            'accounts' => $as->getDashboard(),
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
        return $this->render('AppBundle:Dashboard:elements.html.twig');
    }

    /**
     * @Route("/workspace", name="workspace")
     */
    public function workspaceAction()
    {
        return $this->render('AppBundle:Dashboard:workspace.html.twig');
    }

    /**
     * @Route("/remind", name="remind")
     */
    public function remindAction()
    {
        return $this->render('AppBundle:Dashboard:remindMe.html.twig');
    }

    /**
     * @Route("/empty", name="empty")
     */
    public function emptyAction()
    {
        return $this->render('empty.html.twig');
    }
}
