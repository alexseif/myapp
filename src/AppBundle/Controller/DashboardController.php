<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\TasksService;
use AppBundle\Service\ReminderService;
use AppBundle\Service\AccountsService;

class DashboardController extends Controller
{

    /**
     *
     * @Route("/", name="dashboard")
     */
    public function dashboardAction(Request $request, TasksService $ts, ReminderService $rs, AccountsService $as)
    {
        $em = $this->getDoctrine()->getManager();

        $earnedLogic = new \AppBundle\Logic\EarnedLogic($em, $this->get('myapp.cost'));

        return $this->render("AppBundle:Dashboard:dashboard.html.twig", array(
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
        ));
    }

    /**
     *
     * @Route("elements", name="elements")
     */
    public function elementsAction(Request $request)
    {
        return $this->render("AppBundle:Dashboard:elements.html.twig");
    }

    /**
     *
     * @Route("workspace", name="workspace")
     */
    public function workspaceAction(Request $request)
    {
        return $this->render("AppBundle:Dashboard:workspace.html.twig");
    }

    /**
     *
     * @Route("remind", name="remind")
     */
    public function remindAction(Request $request)
    {
        return $this->render("AppBundle:Dashboard:remindMe.html.twig");
    }

    /**
     *
     * @Route("/workarea", name="workarea")
     */
    public function workareaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $focusTasks = $em->getRepository('AppBundle:Tasks')->focusLimitList();
        $days = $em->getRepository('AppBundle:Days')->getImportantCards();
        $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
        /** Cost Of Life * */
        $costOfLife = $this->get('myapp.cost');
        $earnedLogic = new \AppBundle\Logic\EarnedLogic($em, $costOfLife);
        $earned = $earnedLogic->getEarned();

        $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
        $issued = 0;
        foreach ($issuedThisMonth as $tm) {
            $issued += abs($tm->getAmount());
        }

        return $this->render("AppBundle:Dashboard:workarea.html.twig", array(
            'focus' => $focusTasks,
            'days' => $days,
            'accounts' => $accounts,
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $costOfLife,
            'issued' => $issued,
        ));
    }

}
