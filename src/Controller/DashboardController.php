<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Logic\EarnedLogic;
use App\Service\AccountsService;
use App\Service\CostService;
use App\Service\ReminderService;
use App\Service\TasksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboardAction(
      TasksService $ts,
      ReminderService $rs,
      AccountsService $as,
      CostService $costService,
      EntityManagerInterface $em
    ) {
        $earnedLogic = new EarnedLogic($em, $costService);
        $accounts = $as->getDashboard();
        return $this->render('dashboard/dashboard.html.twig', [
          'taskLists' => $ts->getDashboardTasklists(),
          'randomTasks' => $ts->getRandom(),
          'days' => $rs->getActiveReminders(),
          'holidays' => $em->getRepository(Holiday::class)->getComingHolidays(),
            //          'accounts' => $accounts,
            //          'earned' => $earnedLogic->getEarned(),
            //          'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
          'tskCnt' => $ts->getCompletedCountPerDayOfTheWeek(),
          'costOfLife' => $costService,
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
