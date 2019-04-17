<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

  /**
   * 
   * @Route("/", name="dashboard")
   * @Template("AppBundle:Dashboard:dashboard.html.twig")
   */
  public function dashboardAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:DashboardTaskLists')->findAll();
    $unlistedTasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
    $randomTasks = $em->getRepository('AppBundle:Tasks')->randomTasks();
    $days = $em->getRepository('AppBundle:Days')->getActiveCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
    $tsksCntDay = $em->getRepository('AppBundle:Tasks')->findTasksCountByDay();
    $estSum = $em->getRepository('AppBundle:Tasks')->sumEst()["est"];
    $today = new \DateTime();
    $endDay = new \DateTime();
    $endDay->add(\DateInterval::createFromDateString($estSum . " minutes"));
    $interval = $endDay->diff($today, true);
    /** Cost Of Life * */
    $earned = ['daily' => 0, 'weekly' => 0, 'monthly' => 0];
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);

    $countByUrgenctAndPriority = $em->getRepository('AppBundle:Tasks')->countByUrgenctAndPriority();
    $piechart = [];
    $piechart['Urgent & Important'] = 0;
    $piechart['Urgent'] = 0;
    $piechart['Important'] = 0;
    $piechart['Normal'] = 0;
    $piechart['Low'] = 0;

    foreach ($countByUrgenctAndPriority as $key => $row) {
      $row['est'] = (int) $row['est'];
      if ($row['urgency']) {
        if ($row['priority']) {
          $piechart['Urgent & Important'] = $row['est'];
        } else {
          $piechart['Urgent'] = $row['est'];
        }
      } elseif ($row['priority'] > 0) {
        $piechart['Important'] = $row['est'];
      } elseif ($row['priority'] < 0) {
        $piechart['Low'] = $row['est'];
      } else {
        $piechart['Normal'] = $row['est'];
      }
    }

    $tskCnt = array();
    foreach ($tsksCntDay as $t) {
      $tskCnt[$t['day_name']] = $t['cnt'];
    }
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }
    $earned['monthly'] = $issued;
    $taskscompletedToday = $em->getRepository('AppBundle:Tasks')->getCompletedToday();
    $completedToday = 0;
    foreach ($taskscompletedToday as $task) {
      $completedToday += $task->getEst();
    }
    $earned['daily'] = ($completedToday / 60) * $costOfLife->getHourly();
    $taskscompletedThisWeek = $em->getRepository('AppBundle:Tasks')->getCompletedThisWeek();
    $completedThisWeek = 0;
    foreach ($taskscompletedThisWeek as $task) {
      $completedThisWeek += $task->getEst();
    }
    $earned['weekly'] = ($completedThisWeek / 60) * $costOfLife->getHourly();

    return array(
      'taskLists' => $taskLists,
      'randomTasks' => $randomTasks,
      'unlistedTasks' => $unlistedTasks,
      'days' => $days,
      'accounts' => $accounts,
      'earned' => $earned,
      'completedToday' => $completedToday,
      'tskCnt' => $tskCnt,
      'interval' => $interval,
      'costOfLife' => $costOfLife,
      'piechart' => $piechart,
    );
  }

  /**
   * 
   * @Route("/workarea", name="workarea")
   * @Template("AppBundle:Dashboard:workarea.html.twig")
   */
  public function workareaAction()
  {
    $em = $this->getDoctrine()->getManager();
    $focusTasks = $em->getRepository('AppBundle:Tasks')->focusLimitList();
    $days = $em->getRepository('AppBundle:Days')->getImportantCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);

    $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }
    return array(
      'focus' => $focusTasks,
      'days' => $days,
      'accounts' => $accounts,
      'costOfLife' => $costOfLife,
      'issued' => $issued,
    );
  }

}
